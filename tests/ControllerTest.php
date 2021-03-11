<?php

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use App\Models\City;
use App\Models\Weather;

class ControllerTest extends TestCase
{
    public ?Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(['verify' => false]);
    }

    protected function tearDown(): void
    {
        $this->client = null;
    }

    public static function setUpBeforeClass(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Weather::truncate();
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $city = City::create([
            'id' => 4,
            'name' => 'test1',
            'latitude' => 11.11,
            'longitude' => 22.22,
        ]);

        $weather = new Weather;
        $weather->city()->associate($city);
        $weather->date = '2021-03-07';
        $weather->weather = 'Cloudy';
        $weather->save();

        $weather = new Weather;
        $weather->city()->associate($city);
        $weather->date = '2021-03-08';
        $weather->weather = 'Rain';
        $weather->save();

        $city = City::create([
            'id' => 6,
            'name' => 'test2',
            'latitude' => 33.11,
            'longitude' => 11.22,
        ]);

        $weather = new Weather;
        $weather->city()->associate($city);
        $weather->date = '2021-03-06';
        $weather->weather = 'Warm';
        $weather->save();

        $weather = new Weather;
        $weather->city()->associate($city);
        $weather->date = '2021-03-07';
        $weather->weather = 'Rain';
        $weather->save();
    }

    public static function tearDownAfterClass(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Weather::truncate();
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function testGetCities(): void
    {
        if ($this->client) {
            $response = $this->client->request('GET', "http://{$_ENV['APP_HOST']}/api/cities");
    
            $this->assertEquals(200, $response->getStatusCode());
    
            $contentType = $response->getHeaders()['Content-Type'][0];
            $this->assertEquals('application/json', $contentType);
    
            $response = json_decode($response->getBody());
            $this->assertEquals(true, $response->status);
            $this->assertIsArray($response->data);
            $this->assertCount(2, $response->data);
    
            $firstCity = $response->data[0];
            $this->assertEquals(4, $firstCity->id);
            $this->assertEquals('test1', $firstCity->name);
            $this->assertEquals(11.11, $firstCity->latitude);
            $this->assertEquals(22.22, $firstCity->longitude);
            $this->assertIsArray($firstCity->weather);
            $this->assertCount(2, $firstCity->weather);
    
            $firstWeatherOfFirstCity = $firstCity->weather[0];
    
            $this->assertEquals('2021-03-07', $firstWeatherOfFirstCity->date);
            $this->assertEquals('Cloudy', $firstWeatherOfFirstCity->weather);
        }
    }

    public function testGetACity(): void
    {
        if ($this->client) {
            $response = $this->client->request('GET', "http://{$_ENV['APP_HOST']}/api/cities/4");
    
            $this->assertEquals(200, $response->getStatusCode());
    
            $contentType = $response->getHeaders()['Content-Type'][0];
            $this->assertEquals('application/json', $contentType);
    
            $response = json_decode($response->getBody());
            $this->assertEquals(true, $response->status);
            $this->assertIsObject($response->data);
    
            $city = $response->data;
    
            $this->assertEquals(4, $city->id);
            $this->assertEquals('test1', $city->name);
            $this->assertEquals(11.11, $city->latitude);
            $this->assertEquals(22.22, $city->longitude);
            $this->assertIsArray($city->weather);
            $this->assertCount(2, $city->weather);
    
            $firstWeatherOfCity = $city->weather[0];
    
            $this->assertEquals('2021-03-07', $firstWeatherOfCity->date);
            $this->assertEquals('Cloudy', $firstWeatherOfCity->weather);
        }
    }

    public function testGetACityWithWrongId(): void
    {
        $this->expectException(ServerException::class);
        if ($this->client) {
            $response = $this->client->request('GET', "http://{$_ENV['APP_HOST']}/api/cities/5");
    
            $this->assertEquals(500, $response->getStatusCode());
    
            $contentType = $response->getHeaders()['Content-Type'][0];
            $this->assertEquals('application/json', $contentType);
    
            $response = json_decode($response->getBody());
            $this->assertEquals(false, $response->status);
            $this->assertEquals('Not found city #4.', $response->error);
        }
    }

    public function testWithWrongUri(): void
    {
        $this->expectException(ClientException::class);
        if ($this->client) {
    
            $response = $this->client->request('GET', "http://{$_ENV['APP_HOST']}/test");
    
            $this->assertEquals(404, $response->getStatusCode());
            $this->assertEquals('Not Found', $response->getBody());
        }
    }
}
