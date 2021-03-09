<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    protected $table = 'weathers';

    public $timestamps = false;

    public $incrementing = false;

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function toArray()
    {
        return [
            'date' => $this->date,
            'weather' => $this->weather,
        ];
    }
}
