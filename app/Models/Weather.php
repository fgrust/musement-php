<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 * @property int $city_id
 * @property string $date
 * @property string $weather
 */
class Weather extends Model
{
    protected $table = 'weathers';

    public $timestamps = false;

    public $incrementing = false;

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * 
     * @return mixed[] 
     */
    public function toArray()
    {
        return [
            'date' => $this->date,
            'weather' => $this->weather,
        ];
    }
}
