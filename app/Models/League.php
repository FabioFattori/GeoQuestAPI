<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $table = 'league';

    protected $fillable = [
        'playerId',
        'position',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId');
    }
}
