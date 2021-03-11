<?php

use PHPUnit\Framework\TestCase;

use App\Models\City;
use App\Models\Weather;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;

class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Weather::truncate();
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Weather::truncate();
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function testCreateCity(): void
    {
        $city = City::create([
            'id' => 4,
            'name' => 'test1',
            'latitude' => 11.11,
            'longitude' => 22.22,
        ]);

        $this->assertEquals(4, $city->id);
        $this->assertEquals('test1', $city->name);
        $this->assertEquals(11.11, $city->latitude);
        $this->assertEquals(22.22, $city->longitude);
    }

    public function testCreateWeatherAssociatedWithCity(): void
    {
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

        $this->assertEquals(4, $weather->city_id);
        $this->assertEquals('2021-03-07', $weather->date);
        $this->assertEquals('Cloudy', $weather->weather);

        $this->assertEquals(4, $city->weathers[0]->city_id);
        $this->assertEquals('2021-03-07', $city->weathers[0]->date);
        $this->assertEquals('Cloudy', $city->weathers[0]->weather);
    }

    public function testCreateWeatherWithPrimaryDuplication(): void
    {
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
        $weather->date = '2021-03-07';
        $weather->weather = 'Rain';

        $this->expectException(QueryException::class);

        $weather->save();
    }

    public function testUpdateOrCreateCity(): void
    {
        $city = City::create([
            'id' => 4,
            'name' => 'test1',
            'latitude' => 11.11,
            'longitude' => 22.22,
        ]);

        $city = City::updateOrCreate(
            [
                'id' => 4,
            ],
            [
                'name' => 'test2',
                'latitude' => 22.22,
                'longitude' => 33.33
            ]
        );

        $this->assertEquals(4, $city->id);
        $this->assertEquals('test2', $city->name);
        $this->assertEquals(22.22, $city->latitude);
        $this->assertEquals(33.33, $city->longitude);

        $city = City::updateOrCreate(
            [
                'id' => 5,
            ],
            [
                'name' => 'test3',
                'latitude' => 22.33,
                'longitude' => 33.22
            ]
        );

        $this->assertEquals(5, $city->id);
        $this->assertEquals('test3', $city->name);
        $this->assertEquals(22.33, $city->latitude);
        $this->assertEquals(33.22, $city->longitude);
    }

    public function testUpdateOrCreateWeather(): void
    {
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

        $weathers = $city->weathers()->where(['date' => '2021-03-07']);

        $this->assertCount(1, $weathers->get());

        $weathers->update(['weather' => 'Rain']);

        $this->assertEquals(4, $weathers->first()->city_id);
        $this->assertEquals('2021-03-07', $weathers->first()->date);
        $this->assertEquals('Rain', $weathers->first()->weather);
    }
}
