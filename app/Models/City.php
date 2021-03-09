<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Weather;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = ['id', 'name', 'latitude', 'longitude'];

    public $incrementing = false;

    public function weathers() {
        return $this->hasMany(Weather::class);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'weather' => $this->weathers,
        ];
    }
}
