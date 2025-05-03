<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectedPoi extends Model
{
    protected $table = 'collected_poi';

    protected $fillable = [
        'playerId',
        'latitude',
        'longitude',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId');
    }
    public function getCoordinates()
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function isTheSameAs($otherPoi)
    {
        return $this->latitude === $otherPoi->latitude && $this->longitude === $otherPoi->longitude;
    }
}
