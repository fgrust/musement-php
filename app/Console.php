<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use App\Models\City;
use App\Models\Weather;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use RuntimeException;

final class MusementException extends Exception
{
    public function __toString()
    {
        return "MusementException.\n";
    }
}

final class Musement
{
    private $cities;

    public $http;

    public function __construct()
    {
        $this->http = new Client(['verify' => false]);
    }

    public function getCities()
    {
        return $this->cities;
    }

    public function setCities($cities)
    {
        return $this->cities = $cities;
    }

    public function fetchListOfCities()
    {
        $promise = $this->http->getAsync($_ENV['MUSEMENT_API_URI']);
        $promise->then(
            function (Response $response) {
                $this->setCities(json_decode($response->getBody(), true));
            },
            function (Exception $e) {
                throw new MusementException();
            }
        )->wait();
    }

    public function fetchWeathers($processCity = null, $processWeather = null)
    {
        $cities = $this->getCities();
        $promises = (function () use ($cities, $processCity) {
            foreach ($cities as $city) {
                if ($processCity) {
                    call_user_func($processCity, $city);
                }

                yield $city['id'] => $this->http->getAsync($_ENV['WEATHER_API_URI'], [
                    'query' => [
                        'key' => $_ENV['WEATHER_API_KEY'],
                        'q' => $city['latitude'] . ',' . $city['longitude'],
                        'days' => 2,
                    ],
                ]);
            }
        })();

        $eachPromise = new EachPromise($promises, [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $cityId) use ($processWeather) {
                $forecast = json_decode($response->getBody(), true);
                if ($processWeather) {
                    call_user_func($processWeather, $forecast, $cityId);
                }
            },
            'rejected' => function (Exception $e) {
                throw new MusementException();
            },
        ]);

        $eachPromise->promise()->wait();
    }

    public static function processCity($city)
    {
        City::updateOrCreate(
            [
                'id' => $city['id']
            ],
            [
                'name' => $city['name'],
                'latitude' => $city['latitude'],
                'longitude' => $city['longitude'],
            ],
        );
    }

    public static function processWeather($forecast, $cityId)
    {
        $city = City::find($cityId);
        if (isset($city) && !empty($city)) {
            if (isset($forecast) && isset($forecast['forecast']) && isset($forecast['forecast']['forecastday'])) {
                $forecastDay = $forecast['forecast']['forecastday'];
                if (count($forecastDay) >= 2) {
                    self::output($city['name'], $forecastDay[0]['day']['condition']['text'], $forecastDay[1]['day']['condition']['text']);
                    foreach ($forecastDay as $f) {
                        $weathers = $city->weathers()->where(['date' => $f['date']]);
                        if ($weathers->count() === 1) {
                            $weathers->update(['weather' => $f['day']['condition']['text']]);
                        } elseif ($weathers->count() === 0) {
                            $weather = new Weather;
                            $weather->city()->associate($city);
                            $weather->date = $f['date'];
                            $weather->weather = $f['day']['condition']['text'];
                            $weather->save();
                        } else {
                            throw new MusementException('Unexpected Exception: Weather table has rows that duplicate with primary key.');
                        }
                    }
                } else {
                    throw new MusementException('Failed to fetch forecast for 2 days.');
                }
            } else {
                throw new MusementException('Response contains no information.');
            }
        } else {
            throw new MusementException("Cannot find the city for #$cityId.");
        }
    }

    public static function output($city, $today, $tomorrow)
    {
        echo "Processed city " . $city . " | " . $today . " - " . $tomorrow . "\n";
    }

    public function run()
    {
        $this->fetchListOfCities();
        $this->fetchWeathers([self::class, 'processCity'], [self::class, 'processWeather']);
    }
}

return true;
