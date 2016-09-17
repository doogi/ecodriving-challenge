<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
function calculateKpH($knots) {
    return (int)$knots/1000 * 1.852;
}

function decodeValue($value) {
    return unpack("Nval", base64_decode($value));
}

function caclulateAcceleration($speedAtBegining, $speedAtEnd, $timeInterval) {
    return ($speedAtEnd-$speedAtBegining)/$timeInterval;
}

$app->get('/trip', function () use ($app) {
    $m = new MongoDB\Client("mongodb://localhost:27017");

    $data = [];

    foreach ($m->data->rows->find() as $row) {
        if (empty($row['loc'])) {
            continue;
        }

        $data[] = [
            'lng' => $row['loc'][0],
            'lat' => $row['loc'][1],
            'timestamp' => strtotime($row['recorded_at'])
        ];
    }

    return (new \Symfony\Component\HttpFoundation\JsonResponse($data))
        ->setEncodingOptions(
            \Symfony\Component\HttpFoundation\JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_PRETTY_PRINT
        );
});

$app->get('/blog', function () use ($app) {
    $accelerationId = "AAAACw==";
    $breakingID = "AAAACg==";
    $m = new MongoDB\Client("mongodb://localhost:27017");

    $data = [];

    foreach ($m->data->rows->find() as $row) {
        if (empty($row['loc'])) {
            continue;
        }

        $data[] = [
            'lng' => $row['loc'][0],
            'lat' => $row['loc'][1],
            'timestamp' => strtotime($row['recorded_at'])
        ];
    }

//    $accelerations = [ ];
//    foreach ($m->data->rows->find(['fields.BEHAVE_ID.b64_value' => $accelerationId]) as $row) {
//        $speedAtBeginning = calculateKpH(decodeValue($row['fields']['BEHAVE_GPS_SPEED_BEGIN']['b64_value'])['val']);
//        $speedAtEnd = calculateKpH(decodeValue($row['fields']['BEHAVE_GPS_SPEED_END']['b64_value'])['val']);
//        $duration = decodeValue($row['fields']['BEHAVE_ELAPSED']['b64_value'])['val'];
//        $accelerations[] = [
//            'speedOnStart' => $speedAtBeginning,
//            'speedOnEnd' => $speedAtEnd,
//            'duration' => $duration,
//            'acceleration' => caclulateAcceleration($speedAtBeginning, $speedAtEnd, $duration/1000)
//        ];
//    };
//
//    $breakings = [ ];
//    foreach ($m->data->rows->find(['fields.BEHAVE_ID.b64_value' => $breakingID]) as $row) {
//        $speedAtBeginning = calculateKpH(decodeValue($row['fields']['BEHAVE_GPS_SPEED_BEGIN']['b64_value'])['val']);
//        $speedAtEnd = calculateKpH(decodeValue($row['fields']['BEHAVE_GPS_SPEED_END']['b64_value'])['val']);
//        $duration = decodeValue($row['fields']['BEHAVE_ELAPSED']['b64_value'])['val'];
//        $breakings[] = [
//            'speedOnStart' => $speedAtBeginning,
//            'speedOnEnd' => $speedAtEnd,
//            'duration' => $duration,
//            'acceleration' => caclulateAcceleration($speedAtBeginning, $speedAtEnd, $duration/1000)
//        ];
//    };
//
//    $data = [ ];
//    $data['accelerations'] = $accelerations;
//    $data['breakings'] = $breakings;

    return (new \Symfony\Component\HttpFoundation\JsonResponse($data))
        ->setEncodingOptions(
            \Symfony\Component\HttpFoundation\JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_PRETTY_PRINT
        );
});
// ... definitions

$app->run();
