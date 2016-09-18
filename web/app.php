<?php

require_once __DIR__.'/../vendor/autoload.php';

use Firebase\Token\TokenException;
use Firebase\Token\TokenGenerator;
    
$app = new Silex\Application();
$app['debug'] = true;

const ACCELERATION_VIOLATION = "AAAACw==";
const BREAKING_VIOLATION = "AAAACg==";

const MAX_BATCH_SIZE = 30;

function calculateKpH($knots) {
    return (int)$knots/1000 * 1.852;
}

function calculateMs($knots) {
    return (int)$knots/1000 * 0.514444;
}

function decodeInt($value) {
    return unpack("Nval", base64_decode($value));
}

function decodeString($value) {
    return implode(array_map("chr", unpack('C*', base64_decode($value))));
}

function caclulateAcceleration($speedAtBegining, $speedAtEnd, $timeInterval) {
    return ($speedAtEnd-$speedAtBegining)/$timeInterval;
}

// Function to calculate square of value - mean
function sd_square($x, $mean) { return pow($x - $mean,2); }

// Function to calculate standard deviation (uses sd_square)
function sd($array) {

// square root of sum of squares devided by N-1
    return sqrt(array_sum(array_map("sd_square", $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) );
}

$m = new MongoDB\Client("mongodb://localhost:27017");

$app->get('/decode-int', function () {
    return new \Symfony\Component\HttpFoundation\Response(implode(array_map("chr", unpack('C*', base64_decode("QjoxMzEwNzI=")))));
});

$app->get('/api', function() use ($app, $m) {
    $rows = [];

    //$howMany = rand(1,MAX_BATCH_SIZE);
    //for simulation
    $howMany = 20;//rand(1,30);

    while($howMany-- > 0) {
        $rows[] = $m->data->rows->findOneAndUpdate(
            ['sent' => ['$exists' => false]],
            ['$set' => ['sent' => true]],
            ['sort' => ['recorded_at' => 1]]
        );
    }

    return new \Symfony\Component\HttpFoundation\JsonResponse($rows);
});

$app->get('/reset-api', function() use ($app, $m) {
    $m->data->rows->updateMany([], ['$unset' => ['sent' => '']]);

    return new \Symfony\Component\HttpFoundation\Response("OK");
});

$app->get('/trip', function () use ($app, $m) {
    $data = [];

    $subRequest = \Symfony\Component\HttpFoundation\Request::create('/api');
    /** @var \Symfony\Component\HttpFoundation\JsonResponse $response */
    $response = $app->handle($subRequest, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST, false);
    $responseContent = json_decode($response->getContent(), true);

    $speedList = [];
    $rpmList = [];
    foreach ($responseContent as $row) {
        if (empty($row['loc'])) {
            continue;
        }

        $points = 0;
        $violations = [];
        $obedience = [];
        if (!empty($row['fields']['BEHAVE_ID']) && $row['fields']['BEHAVE_ID']['b64_value'] === ACCELERATION_VIOLATION) {
            $points += 10;
            $beginingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_BEGIN']['b64_value'])['val']);
            $endingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_END']['b64_value'])['val']);
            $duration = decodeInt($row['fields']['BEHAVE_ELAPSED']['b64_value'])['val']/1000;
            $obedience[] = [
                'id' => 'LIGHT_SPEED_OBEDIENCE',
                'desc' => sprintf('You accelerated from %d km/h to %d km/h in %.2f seconds, now try to keep the speed!', $beginingSpeed, $endingSpeed, $duration)
            ];
        }

        if (!empty($row['fields']['BEHAVE_ID']) && $row['fields']['BEHAVE_ID']['b64_value'] === BREAKING_VIOLATION) {
            $points -= 10;
            $beginingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_BEGIN']['b64_value'])['val']);
            $endingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_END']['b64_value'])['val']);
            $duration = decodeInt($row['fields']['BEHAVE_ELAPSED']['b64_value'])['val']/1000;
            $violations[] = [
                'id' => 'HARD_BREAKS_VIOLATION',
                'desc' => sprintf('You decelerated from %d km/h to %d km/h in %.2f seconds!', $beginingSpeed, $endingSpeed, $duration)
            ];
        }

        if (!empty($row['fields']['MDI_OBD_RPM'])) {
            $rpm = decodeInt($row['fields']['MDI_OBD_RPM']['b64_value'])['val'];
            if (!empty($rpm)) {
                $rpmList[] = $rpm;
                if ($rpm > 2000) {
                    $points -= 10;
                    $violations[] = [
                        'id' => 'RPM_VIOLATION',
                        'desc' => sprintf('Whoa! You reached %d RPMs, fuel is disappearing like in black hole!', $rpm)
                    ];
                }
            }
        }

        if (!empty($row['fields']['MDI_OBD_SPEED'])) {
            $speed = decodeInt($row['fields']['MDI_OBD_SPEED']['b64_value'])['val'];
            if (!empty($speed)) {
                $speedList[] = $speed;
                if ($speed > 130) {
                    $speedList = [];
                    $points -= 10;
                    $violations[] = [
                        'id' => 'OVERSPEED_VIOLATION',
                        'desc' => sprintf('Hey Bandit, keep calm and slow down a little, you\'re not in a plane, %d km/h is too much', $speed)
                    ];
                }
            }
        }

        $data[] = [
            'lng' => $row['loc'][0],
            'lat' => $row['loc'][1],
            'timestamp' => strtotime($row['recorded_at']),
            'points' => $points,
            'violations' => $violations,
            'obedience' => $obedience
        ];
    }

    if (count($speedList) > 5) {
        $standardDeviation = sd($speedList);
        $lastEntry = end($data);

        $averageSpeed = array_sum($speedList)/count($speedList);
        if ($standardDeviation < 10 && $averageSpeed < 100) {
            $data[] = [
                'lng' => $lastEntry['lng'],
                'lat' => $lastEntry['lat'],
                'timestamp' => $lastEntry['timestamp'],
                'points' => 10,
                'violations' => [],
                'obedience' => [
                    'id' => 'EQUAL_SPEED_OBEDIENCE',
                    'desc' => sprintf('You kept your speed (%.2f km/h) for a while, good job!', $averageSpeed)
                ]
            ];
        }
    }

    if (count($rpmList) > 3) {
        $standardDeviation = sd($rpmList);
        $lastEntry = end($data);

        $averageRpm = array_sum($rpmList)/count($speedList);
        if ($standardDeviation < 1000 && $averageRpm > 1500) {
            $data[] = [
                'lng' => $lastEntry['lng'],
                'lat' => $lastEntry['lat'],
                'timestamp' => $lastEntry['timestamp'],
                'points' => 10,
                'violations' => [],
                'obedience' => [
                    'id' => 'EQUAL_RPM_OBEDIENCE',
                    'desc' => sprintf('You kept your RPM (%.2f RPM) for a while, good job!', $averageRpm)
                ]
            ];
        }
    }
    
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhZG1pbiI6ZmFsc2UsImRlYnVnIjpmYWxzZSwiZCI6eyJ1aWQiOiIzb1V6WDZNalllVGFIUHhZbVJNNjJkOVI2ZHUxIn0sInYiOjAsImlhdCI6MTQ3NDE0NzMwNX0.LQU56Fga9OgY3hyadLw1gate0L0rFEKwVOAPRAQVhuQ';

    foreach ($data as $element) {
        //test only
        if ($element['points'] === 0 && rand(0,10) < 5) {
            $element['points'] = rand(-10,10);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://ecodrivingchallange.firebaseio.com/vechicle/YfM102C8grQbdSor7wh6EBIZXwG2/data.json?auth=" . $token);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($element));
        $data = curl_exec($ch);
        curl_close($ch);
//        if (rand(1, 10) <= 7) {
//            sleep(rand(1, 2));
//        }
    }

    return 'OK';
    return (new \Symfony\Component\HttpFoundation\JsonResponse($data))
        ->setEncodingOptions(
            \Symfony\Component\HttpFoundation\JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_PRETTY_PRINT
        );
});

$app->get('/trips', function () use ($app, $m) {
    $data = [];


    $speedList = [];
    $rpmList = [];
    $count = 0;
    foreach ($m->data->rows->find([],['sort' => ['recorded_at' => 1]]) as $row) {
        if (empty($row['loc'])) {
            continue;
        }

        $points = 0;
        $violations = [];
        $obedience = [];
        if (!empty($row['fields']['BEHAVE_ID']) && $row['fields']['BEHAVE_ID']['b64_value'] === ACCELERATION_VIOLATION) {
            $points += 10;
            $beginingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_BEGIN']['b64_value'])['val']);
            $endingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_END']['b64_value'])['val']);
            $duration = decodeInt($row['fields']['BEHAVE_ELAPSED']['b64_value'])['val']/1000;
            $obedience[] = [
                'id' => 'LIGHT_SPEED_OBEDIENCE',
                'desc' => sprintf('You accelerated from %d km/h to %d km/h in %.2f seconds, now try to keep the speed!', $beginingSpeed, $endingSpeed, $duration)
            ];
        }

        if (!empty($row['fields']['BEHAVE_ID']) && $row['fields']['BEHAVE_ID']['b64_value'] === BREAKING_VIOLATION) {
            $points -= 10;
            $beginingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_BEGIN']['b64_value'])['val']);
            $endingSpeed = calculateKpH(decodeInt($row['fields']['BEHAVE_GPS_SPEED_END']['b64_value'])['val']);
            $duration = decodeInt($row['fields']['BEHAVE_ELAPSED']['b64_value'])['val']/1000;
            $violations[] = [
                'id' => 'HARD_BREAKS_VIOLATION',
                'desc' => sprintf('You decelerated from %d km/h to %d km/h in %.2f seconds!', $beginingSpeed, $endingSpeed, $duration)
            ];
        }

        if (!empty($row['fields']['MDI_OBD_RPM'])) {
            $rpm = decodeInt($row['fields']['MDI_OBD_RPM']['b64_value'])['val'];
            if (!empty($rpm)) {
                $rpmList[] = $rpm;
                if ($rpm > 2000) {
                    $points -= 10;
                    $violations[] = [
                        'id' => 'RPM_VIOLATION',
                        'desc' => sprintf('Whoa! You reached %d RPMs, fuel is disappearing like in black hole!', $rpm)
                    ];
                }
            }
        }

        if (!empty($row['fields']['MDI_OBD_SPEED'])) {
            $speed = decodeInt($row['fields']['MDI_OBD_SPEED']['b64_value'])['val'];
            if (!empty($speed)) {
                $speedList[] = $speed;
                if ($speed > 130) {
                    $speedList = [];
                    $points -= 10;
                    $violations[] = [
                        'id' => 'OVERSPEED_VIOLATION',
                        'desc' => sprintf('Hey Bandit, keep calm and slow down a little, you\'re not in a plane, %d km/h is too much', $speed)
                    ];
                }
            }
        }

        $data[] = [
            'lng' => $row['loc'][0],
            'lat' => $row['loc'][1],
            'timestamp' => strtotime($row['recorded_at']),
            'points' => $points,
            'violations' => $violations,
            'obedience' => $obedience
        ];

        if ($count++ > MAX_BATCH_SIZE) {
            if (count($speedList) > 5) {
                $standardDeviation = sd($speedList);
                $lastEntry = end($data);

                $averageSpeed = array_sum($speedList)/count($speedList);
                if ($standardDeviation < 10 && $averageSpeed < 100) {
                    $data[] = [
                        'lng' => $lastEntry['lng'],
                        'lat' => $lastEntry['lat'],
                        'timestamp' => $lastEntry['timestamp'],
                        'points' => 10,
                        'violations' => [],
                        'obedience' => [
                            'id' => 'EQUAL_SPEED_OBEDIENCE',
                            'desc' => sprintf('You kept your speed (%.2f km/h) for a while, good job!', $averageSpeed)
                        ]
                    ];
                }
            }

            if (count($rpmList) > 3) {
                $standardDeviation = sd($rpmList);
                $lastEntry = end($data);

                $averageRpm = array_sum($rpmList)/count($speedList);
                if ($standardDeviation < 1000 && $averageRpm > 1500) {
                    $data[] = [
                        'lng' => $lastEntry['lng'],
                        'lat' => $lastEntry['lat'],
                        'timestamp' => $lastEntry['timestamp'],
                        'points' => 10,
                        'violations' => [],
                        'obedience' => [
                            'id' => 'EQUAL_RPM_OBEDIENCE',
                            'desc' => sprintf('You kept your RPM (%.2f RPM) for a while, good job!', $averageRpm)
                        ]
                    ];
                }
            }

            $speedList = [];
            $rpmList = [];
            $count = 0;
        }
    }


    return (new \Symfony\Component\HttpFoundation\JsonResponse($data))
        ->setEncodingOptions(
            \Symfony\Component\HttpFoundation\JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_PRETTY_PRINT
        );
});

$app->run();
