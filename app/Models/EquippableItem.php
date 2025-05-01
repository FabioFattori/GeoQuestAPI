<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquippableItem extends Model
{
    use HasFactory;

    protected $table = 'equippableItems';
    protected $fillable = [
        'rarityId',
        'blueprintId',
        'ownerId',
        'randomFactor'
    ];

    protected $casts = [
        'randomFactor' => 'float',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'randomFactor',
    ];


    protected $appends = ['damage', 'health', 'blueprint', 'rarity'];

    public function getBlueprintAttribute()
    {
        return $this->blueprint()->first();
    }

    public function getRarityAttribute()
    {
        return $this->rarity()->first();
    }

    public function rarity()
    {
        return $this->belongsTo(Rarity::class, 'rarityId', 'id');
    }
    public function owner()
    {
        return $this->belongsTo(Player::class, 'ownerId', 'id');
    }

    public function blueprint()
    {
        return $this->belongsTo(EquippableItemBlueprint::class, 'blueprintId', 'id');
    }

    protected function calculateRandomValue($baseValue)
    {
        return ($baseValue + $this->randomFactor) * $this->rarity->damageMultiplier;
    }

    public function getDamageAttribute()
    {
        return $this->calculateRandomValue($this->blueprint->baseDamage);
    }

    public function getHealthAttribute()
    {
        return $this->calculateRandomValue($this->blueprint->baseHealth);
    }
}
