<?php

$autoloader = __DIR__.'/../vendor/autoload.php';

if(!file_exists($autoloader)) {
    die( 'You must run `composer install` in the sample app directory' );
}

require $autoloader;

use Slim\Slim;

use ICanBoogie\Storage\APCStorage;
use ICanBoogie\Storage\FileStorage;

use OpenTok\OpenTok;
use OpenTok\MediaMode;

// PHP CLI webserver compatibility, serving static files
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

// Verify that the API Key and API Secret are defined
if (!(getenv('API_KEY') && getenv('API_SECRET'))) {
    die('You must define an API_KEY and API_SECRET in the run-demo file');
}

// Instantiate a Slim app
$app = new Slim(array(
    'log.enabled' => true
    //'templates.path' => '../templates'
));

$app->config('mode', getenv('SLIM_MODE'));

// Intialize storage interface wrapper, store it in a singleton
$app->container->singleton('storage', function() use ($app) {
    // If the SLIM_MODE environment variable is set to 'production' (like on Heroku) the APC is used as 
    // the storage backed. Otherwise (like running locally) the filesystem is used as the storage 
    // backend.
    $storage = null;
    $mode = $app->config('mode');
    if ($mode === 'production') {
        $storage = new APCStorage();
    } else {
        $storage = new FileStorage('storage');
    }
    return $storage;
});

// Initialize OpenTok instance, store it in the app contianer
$app->container->singleton('opentok', function () {
        return new OpenTok(getenv('API_KEY'), getenv('API_SECRET'));
});

// Store the API Key in the app container
$app->apiKey = getenv('API_KEY');

// If a sessionId has already been created, retrieve it from the storage
$app->container->singleton('sessionId', function() use ($app) {
    if ($app->storage->exists('sessionId')) {
        return $app->storage->retrieve('sessionId');
    }

    $session = $app->opentok->createSession(array(
        'mediaMode' => MediaMode::ROUTED
    ));
    $app->storage->store('sessionId', $session->getSessionId());
    return $session->getSessionId();
});

$app->get('/debug', 'cors', function () use ($app) {

    $token = $app->opentok->generateToken($app->sessionId);

    $responseData = array(
        'apiKey' => $app->apiKey,
        'mode' => $app->config('mode')
    );

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($responseData);
});

$app->get('/session/:role', 'cors', function ($role) use ($app) {

    $token = $app->opentok->generateToken($app->sessionId, array('role' => $role));

    $responseData = array(
        'apiKey' => $app->apiKey,
        'sessionId' => $app->sessionId,
        'token'=> $token
    );

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($responseData);
});

$app->post('/broadcast/start', 'cors', function () use ($app) {
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $sessionId = $data["sessionId"];

    $broadcast = $app->opentok->startBroadcast($sessionId);

    $app->response->headers->set('Content-Type', 'application/json');

    echo json_encode($broadcast->jsonSerialize());
});

$app->post('/broadcast/stop', 'cors', function () use ($app) {
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $broadcastId = $data["broadcastId"];

    $broadcast = $app->opentok->stopBroadcast($broadcastId);

    $app->response->headers->set('Content-Type', 'application/json');

    echo json_encode($broadcast->jsonSerialize());
});

$app->get('/hls', 'cors', function () use ($app) {
    $url = $app->request()->params('url');
    $availableAt = $app->request()->params('availableAt');
    $data ='{"url":"' . $url . '","availableAt":"' . $availableAt . '"}';

    $app->render('hls.php', array('data' => $data));
});


// Enable CORS functionality
function cors() {
    // Allow from any origin
    if (isset( $_SERVER['HTTP_ORIGIN'])) {
        header( "Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}" );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Max-Age: 86400' );    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    }
}

// return HTTP 200 for HTTP OPTIONS requests
$app->map('/:x+', function($x) {
        http_response_code( 200 );
})->via('OPTIONS');

// TODO: route to clear storage
$app->post('/session/clear', function() use ($app) {
    if ($app->storage instanceof APCStorage) {
        $app->storage->clear();
    }
});

$app->run();
