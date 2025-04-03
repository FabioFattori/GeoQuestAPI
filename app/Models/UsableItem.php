<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsableItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'durationInSeconds',
        'healthRecovery',
        'damageAplifier',
    ];

    protected $casts = [
        'durationInSeconds' => 'integer',
        'healthRecovery' => 'integer',
        'damageAplifier' => 'integer',
    ];

    public function getOwner()
    {
        return $this->belongsTo(Player::class, 'ownerId', 'id');
    }
}
