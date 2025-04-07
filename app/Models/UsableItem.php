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
        'durationInSeconds',
        'healthRecovery',
        'damageAplifier',
    ];

    protected $casts = [
        'durationInSeconds' => 'integer',
        'healthRecovery' => 'integer',
        'damageAplifier' => 'integer',
    ];

    public function getRarityAttribute()
    {
        return Rarity::find($this->rarityId);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'usable_items_users')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
