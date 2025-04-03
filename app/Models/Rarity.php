<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rarity extends Model
{
    protected $fillable = [
        'name',
        'hexColor',
        'multiplier'
    ];

    protected $casts = [
        'name' => 'string',
        'hexColor' => 'string'
    ];

    public function getEquippableItems()
    {
        return $this->hasMany(EquippableItem::class, 'rarityId', 'id');
    }
}
