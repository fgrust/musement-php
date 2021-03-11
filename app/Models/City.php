<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Weather;

/**
 * 
 * @property int $id
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 */
class City extends Model
{
    protected $table = 'cities';

    protected $fillable = ['id', 'name', 'latitude', 'longitude'];

    public $incrementing = false;

    public function weathers(): HasMany
    {
        return $this->hasMany(Weather::class);
    }

    /**
     * 
     * @return mixed[] 
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            // @phpstan-ignore-next-line
            'weather' => $this->weathers,
        ];
    }
}
