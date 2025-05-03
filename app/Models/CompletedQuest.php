<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedQuest extends Model
{
    protected $table = 'completed_quest';

    protected $fillable = [
        'playerId',
        'name',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId');
    }

    public function getName()
    {
        return $this->name;
    }
}
