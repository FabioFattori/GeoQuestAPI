<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rarity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'hexColor',
        'multiplier',
        'levelRequiredToDrop'
    ];

    protected $casts = [
        'name' => 'string',
        'hexColor' => 'string'
    ];

    public function getEquippableItems()
    {
        return $this->hasMany(EquippableItem::class, 'rarityId', 'id');
    }

    public function getConsumableItems()
    {
        return $this->hasMany(UsableItem::class, 'rarityId', 'id');
    }

    public static function getPossibleRaritiesGivenLevel($level)
    {
        return Rarity::where('levelRequiredToDrop', '<=', $level)->get();
    }
}
