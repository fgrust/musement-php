<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use App\Models\City;
use App\Models\Weather;

use function PHPUnit\Framework\isEmpty;

final class MusementException extends Exception
{
    public function __toString()
    {
        return "MusementException.\n";
    }
}

final class Musement
{
    private $cities = array();

    public function getCities(): array
    {
        return $this->cities;
    }

    public function fetchListOfCities()
    {
        $client = new Client(['verify' => false]);

        try {
            $response = $client->request('GET', $_ENV['MUSEMENT_API_URI']);
            $this->cities = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new MusementException($e);
        }
    }

    public function fetchWeather($city)
    {
        $client = new Client(['verify' => false]);

        try {
            $response = $client->request('GET', $_ENV['WEATHER_API_URI'], [
                'query' => [
                    'key' => $_ENV['WEATHER_API_KEY'],
                    'q' => $city['latitude'] . ',' . $city['longitude'],
                    'days' => 2,
                ],
            ]);

            $response = json_decode($response->getBody(), true);

            if (isset($response) && isset($response['forecast']) && isset($response['forecast']['forecastday'])) {
                $forecastDay = $response['forecast']['forecastday'];
                if (count($forecastDay) >= 2) {
                    self::output($city['name'], $forecastDay[0]['day']['condition']['text'], $forecastDay[1]['day']['condition']['text']);
                    return $forecastDay;
                } else {
                    throw new MusementException('Failed to fetch forecast for 2 days.');
                }
            } else {
                throw new MusementException('Response contains no information.');
            }
        } catch (Exception $e) {
            throw new MusementException();
        }

        return [];
    }

    public static function output($city, $today, $tomorrow)
    {
        echo "Processed city " . $city . " | " . $today . " - " . $tomorrow . "\n";
    }

    public function run()
    {
        $this->fetchListOfCities();
        foreach ($this->cities as $city) {
            $city = City::updateOrCreate(
                [
                    'id' => $city['id']
                ],
                [
                    'name' => $city['name'],
                    'latitude' => $city['latitude'],
                    'longitude' => $city['longitude'],
                ],
            );

            $forecastDay = $this->fetchWeather($city);

            if (!empty($forecastDay)) {
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
                    } else {}
                }
            }
        }
    }
}

return true;
