<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsableItem extends Model
{
    use HasFactory;
    protected $table = 'usableItems'; 
    protected $fillable = [
        'name',
        'description',
        'healthRecovery',
        'imagePath',
        'ownerId',
        'rarityId',
    ];

    protected $casts = [
        'durationInSeconds' => 'integer',
        'healthRecovery' => 'integer',
        'damageAplifier' => 'integer',
    ];

    protected $appends = ['rarity'];

    public function getRarityAttribute()
    {
        return Rarity::find($this->rarityId);
    }

    public function owner()
    {
        return $this->belongsTo(Player::class, 'ownerId', 'id');
    }
}
