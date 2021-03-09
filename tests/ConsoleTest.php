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

        $city = $musement->getCities()[rand(0, count($musement->getCities()))];

        ob_start();
        $musement->fetchWeather($city);
        $this->assertStringContainsString(
            "Processed city " . $city['name'] . " | ",
            ob_get_contents()
        );
        ob_end_clean();
    }

    public function testFetchWeatherExceptionWithAPIWrongDomain()
    {
        $musement = new Musement();
        $musement->fetchListOfCities();

        $city = $musement->getCities()[rand(0, count($musement->getCities()))];

        $_ENV['WEATHER_API_URI'] = "https://request.exception.com";

        $this->expectException(MusementException::class);
        $musement->fetchWeather($city);
    }

    public function testFetchWeatherExceptionWithAPIWrongKey()
    {
        $musement = new Musement();
        $musement->fetchListOfCities();

        $city = $musement->getCities()[rand(0, count($musement->getCities()))];

        $_ENV['WEATHER_API_KEY'] = "wrongkey";

        $this->expectException(MusementException::class);
        $musement->fetchWeather($city);
    }

    public function testFetchWeatherExceptionWithWrongParams()
    {
        $musement = new Musement();
        $musement->fetchListOfCities();

        $city = array();

        $this->expectException(MusementException::class);
        $musement->fetchWeather($city);
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
