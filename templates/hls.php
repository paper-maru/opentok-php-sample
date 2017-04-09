<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HLS Viewer</title>

    <link rel="stylesheet" href="public/css/styles.css">
    <link href='https://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ramda/0.21.0/ramda.min.js" type="text/javascript" defer></script>
    <script src="https://cdn.jsdelivr.net/es6-promise/3.1.2/es6-promise.min.js" type="text/javascript" defer></script>
    <script src="https://static.opentok.com/v2/js/opentok.min.js" type="text/javascript" defer></script>

    <link href='https://fonts.googleapis.com/css?family=Yellowtail' rel='stylesheet' type='text/css'>
    <script src="https://releases.flowplayer.org/6.0.5/flowplayer.min.js" defer></script>
    <script src="https://releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js" defer></script>
    <script src="public/js/broadcast.js" type="text/javascript"></script>
</head>

<body>
    <div id="broadcast" style="display:none;" data='<?php echo $data;?>'></div>
    <div id="main" class="main-container player">
        <div id="banner" class="banner">
            <span id="bannerText" class="text">Waiting for Broadcast to Begin</span>
        </div>
        <div id="videoContainer" class="video-container player"></div>
    </div>
</body>

</html>
