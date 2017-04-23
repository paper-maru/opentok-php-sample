# OpenTok Broadcast Sample

forked from [opentok/learning-opentok-php](https://github.com/opentok/learning-opentok-php)

## Installation

1. Clone this repository.

2. Use [Composer](https://getcomposer.org/) to install the dependencies:

    `$ composer install --ignore-platform-reqs`

3. Next, input your own API Key and API Secret into the `run-demo` script file:

    ```
    export API_KEY=0000000
    export API_SECRET=abcdef1234567890abcdef01234567890abcdef
    ```

4. The run-demo file starts the PHP CLI development server (requires PHP >= 5.4) on port 8080. If
you want to run your server on another port, edit the file. Finally, start the server using the
run-demo script:

    `$ ./run-demo`

5. Visit the URL <http://localhost:8080/debug> in your browser.


Now we are ready to configure the HTTP routes for our sample app. We need four routes for our app:

1. Generate a host session and Publish the stream
2. Generate a guest session and Join the stream
3. Start broadcast
4. View HLS Stream
5. Stop broadcast

## API Endpoint

### Debug

#### Config

Show config vars.

```
GET /debug
```

##### Curl Example

```bash
$ curl https://*.herokuapp.com/debug
{
  "apiKey": "****",
  "mode": "production"
}
```

### HLS View

```
GET /hls
```

#### Query Parameters

| Name | Type | Description | Example |
| ------- | ------- | ------- | ------- |
| **url** | *string* | HLS URL | `"https://cdn-broadcast002-iad.tokbox.com/20666/20666_dca9aebc-8cdd-4313-a260-b36ffc147ac7.smil/playlist.m3u8"` |
| **availableAt** | *string* | Unixtime | `"1491536899079"` |

#### Open your browser

```
https://*.herokuapp.com/hls?url=https://cdn-broadcast002-iad.tokbox.com/20666/20666_dca9aebc-8cdd-4313-a260-b36ffc147ac7.smil/playlist.m3u8&availableAt=1491536899079
```

### Session

#### Get session

```
GET /session/:role
```

#### Query Parameters

- host: 'moderator'
- guest: 'publisher'
- viewer: 'subscriber'

##### Curl Example

```bash
$ curl https://*.herokuapp.com/session/publisher
{
  "apiKey": "****",
  "sessionId": "****",
  "token": "****"
}
```

### Broadcast

#### Start

```
POST /broadcast/start
```

##### Parameters

| Name | Type | Description | Example |
| ------- | ------- | ------- | ------- |
| **sessionId** | *string* | Session ID | `"hogehoge"` |

##### Curl Example

```bash
$ curl -X POST "https://*.herokuapp.com/broadcast/start"
-d '{"sessionId": "hogehoge"}'
{
  "id": "*",
  "sessionId": "hogehoge",
  "projectId": 1,
  "createdAt": 1491738710532,
  "broadcastUrls": {
    "hls": "https://cdn-broadcast002-iad.tokbox.com/20666/20666_dca9aebc-8cdd-4313-a260-b36ffc147ac7.smil/playlist.m3u8"
  },
  "updatedAt": 1491738713409,
  "status": "started",
  "partnerId": 2
}
```

#### Stop

```
POST /broadcast/stop
```

##### Parameters

| Name | Type | Description | Example |
| ------- | ------- | ------- | ------- |
| **broadcastId** | *string* | Broadcast ID | `"hogehoge"` |

##### Curl Example

```bash
$ curl -X POST "https://*.herokuapp.com/broadcast/stop"
-d '{"broadcastId": "hogehoge"}'
{
  "id": "hogehoge",
  "sessionId": "hogehoge",
  "projectId": 1,
  "createdAt": 1491738713410,
  "broadcastUrls": null,
  "updatedAt": 1491738713410,
  "status": "stopped",
  "partnerId": 2
}
```

#### Get

```
GET /broadcast/:broadcastId
```

##### Parameters

| Name | Type | Description | Example |
| ------- | ------- | ------- | ------- |
| **broadcastId** | *string* | Broadcast ID | `"hogehoge"` |

##### Curl Example

```bash
$ curl "https://*.herokuapp.com/broadcast/hogehoge"
{
  "id": "hogehoge",
  "sessionId": "hogehoge",
  "projectId": 1,
  "createdAt": 1491738710532,
  "broadcastUrls": {
    "hls": "https://cdn-broadcast002-iad.tokbox.com/20666/20666_dca9aebc-8cdd-4313-a260-b36ffc147ac7.smil/playlist.m3u8"
  },
  "updatedAt": 1491738713409,
  "status": "started",
  "partnerId": 2
}
```

## Appendix -- Deploying to Heroku

If you'd like to deploy manually, here is some additional information:

*  The provided `Procfile` describes a web process which can launch this application.

*  Use Heroku config to set the following keys:

   -  `OPENTOK_KEY` -- Your OpenTok API Key
   -  `OPENTOK_SECRET` -- Your OpenTok API Secret
   -  `SLIM_MODE` -- Set this to `production` when the environment variables should be used to
      configure the application. The Slim application will only start reading its Heroku config when
      its mode is set to `'production'`

   You should avoid committing configuration and secrets to your code, and instead use Heroku's
   config functionality.
