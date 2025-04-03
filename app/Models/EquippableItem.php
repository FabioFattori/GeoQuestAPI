<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquippableItem extends Model
{
    protected $fillable = [
        'rarityId',
        'blueprintId',
        'ownerId',
        'randomFactor'
    ];

    protected $casts = [
        'rarityId' => Rarity::class,
        'blueprintId' => EquippableItemBlueprint::class,
        'ownerId' => Player::class,
        'randomFactor' => 'float',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'randomFactor',
    ];

    
    protected $appends = ['damage','health','blueprint'];


    public function getRarity()
    {
        return $this->belongsTo(Rarity::class, 'rarityId', 'id');
    }
    public function getOwner()
    {
        return $this->belongsTo(Player::class, 'ownerId', 'id');
    }

    public function getBlueprint()
    {
        return $this->belongsTo(EquippableItemBlueprint::class, 'blueprintId', 'id');
    }

    protected function calculateRandomValue($baseValue)
    {
        return ($baseValue + $this->randomFactor) * $this->getRarity->damageMultiplier;
    }

    public function getDamageAttribute()
    {
        return $this->calculateRandomValue($this->getBlueprint->baseDamage);
    }

    public function getHealthAttribute()
    {
        return $this->calculateRandomValue($this->getBlueprint->baseHealth);
    }

    public function getBlueprintAttribute()
    {
        return $this->getBlueprint;
    }
}
