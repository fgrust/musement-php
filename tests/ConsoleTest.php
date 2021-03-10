<?php

use PHPUnit\Framework\TestCase;

use App\Musement;
use App\MusementException;

class MusementTest extends TestCase
{
    protected function setUp(): void
    {
        $_ENV['MUSEMENT_API_URI'] = "https://api.musement.com/api/v3/cities";
        $_ENV['WEATHER_API_URI'] = "https://api.weatherapi.com/v1/forecast.json";
        $_ENV['WEATHER_API_KEY'] = "87afad46272d4c35975141130210303";
    }

    public function testFetchListOfCities()
    {
        $musement = new Musement();
        $musement->fetchListOfCities();

        $this->assertIsArray($musement->getCities());
        $this->assertNotEmpty($musement->getCities());

        $ind = rand(0, count($musement->getCities()));
        $city = $musement->getCities()[$ind];

        $this->assertIsArray($city);
        $this->assertArrayHasKey('name', $city);
        $this->assertIsString($city['name']);
        $this->assertArrayHasKey('latitude', $city);
        $this->assertIsNumeric($city['latitude']);
        $this->assertArrayHasKey('longitude', $city);
        $this->assertIsNumeric($city['longitude']);
    }

    public function testFetchListOfCitiesExceptionWithAPIWrongDomain()
    {
        $musement = new Musement();
        $_ENV['MUSEMENT_API_URI'] = 'https://request.exception.com';

        $this->expectException(MusementException::class);
        $musement->fetchListOfCities();
    }

    public function testFetchListOfCitiesExceptionWithAPIWrongParams()
    {
        $musement = new Musement();
        $_ENV['MUSEMENT_API_URI'] = 'https://api.musement.com/api/v3/citi';

        $this->expectException(MusementException::class);
        $musement->fetchListOfCities();
    }

    public function testFetchWeather()
    {
        $musement = new Musement();
        $musement->fetchListOfCities();

        $musement->fetchWeathers(
            function ($city) {
                $this->assertIsArray($city);
                $this->assertArrayHasKey('name', $city);
                $this->assertIsString($city['name']);
                $this->assertArrayHasKey('latitude', $city);
                $this->assertIsNumeric($city['latitude']);
                $this->assertArrayHasKey('longitude', $city);
                $this->assertIsNumeric($city['longitude']);
            },
            function ($forecast, $cityId) use ($musement) {
                $this->assertIsArray($forecast);
                $this->assertArrayHasKey('forecast', $forecast);
                $this->assertIsArray($forecast['forecast']);
                $this->assertArrayHasKey('forecastday', $forecast['forecast']);
                $forecastDay = $forecast['forecast']['forecastday'];
                $this->assertIsIterable($forecastDay);
                $this->assertIsArray($forecastDay[0]);
                $this->assertArrayHasKey('day', $forecastDay[0]);
                $this->assertIsArray($forecastDay[0]['day']);
                $this->assertArrayHasKey('condition', $forecastDay[0]['day']);
                $this->assertIsArray($forecastDay[0]['day']['condition']);
                $this->assertArrayHasKey('text', $forecastDay[0]['day']['condition']);
                $this->assertIsString($forecastDay[0]['day']['condition']['text']);
                $this->assertArrayHasKey('date', $forecastDay[0]);
                $this->assertIsString($forecastDay[0]['date']);

                $this->assertEquals(true, (function () use ($cityId, $musement) {
                    foreach ($musement->getCities() as $city) {
                        if ($city['id'] === $cityId) {return true;}
                    }
                    return false;
                })());
            }
        );
    }

    public function testFetchWeatherExceptionWithAPIWrongDomain()
    {
        $musement = new Musement();
        $musement->fetchListOfCities();

        $_ENV['WEATHER_API_URI'] = "https://request.exception.com";

        $this->expectException(MusementException::class);
        $musement->fetchWeathers();
    }

    public function testFetchWeatherExceptionWithAPIWrongKey()
    {
        $musement = new Musement();
        $musement->fetchListOfCities();

        $_ENV['WEATHER_API_KEY'] = "wrongkey";

        $this->expectException(MusementException::class);
        $musement->fetchWeathers();
    }

    public function testOutput()
    {
        ob_start();
        Musement::output('Amsterdam', 'Rain', 'Cloudy');
        $this->assertEquals(
            "Processed city Amsterdam | Rain - Cloudy\n",
            ob_get_contents()
        );
        ob_end_clean();
    }
}
