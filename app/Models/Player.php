<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'level',
        'experienceCollected',
        'nWonBattles',
        'nBattles',
        'helmetId',
        'runeId',
        'weaponId',
        'currentHealth',
        'maxLevel'
    ];

    protected $casts = [
        'level' => 'integer',
        'experienceCollected' => 'integer',
        'nWonBattles' => 'integer',
        'nBattles' => 'integer'
    ];

    protected $hidden = [
        "createdAt",
        "updatedAt"
    ];

    protected $appends = ['experienceNeeded', 'damage', 'maxHealth','helmet', 'rune', 'weapon', 'experienceToLevelUp'];

    private static function linearFunction($a, $b, $level)
    {
        $level = max($level, 1);
        return ceil($a * $level + $b);
    }

    public function getDamageAttribute()
    {
        return Player::linearFunction(0.6, 1, $this->level);
    }

    public function getMaxHealthAttribute()
    {
        return Player::linearFunction(0.6, 14, $this->level);
    }

    public function getExperienceNeededAttribute()
    {
        if ($this->level <= 0) {
            $this->level = 1;
        }
        $a = 50;
        $b = 150;
        $c = 500;
        $d = 200;
        return ceil($a * $this->level ** 2 + $b * $this->level + $c + $d * $d * log($this->level));
    }

    public function getExperienceToLevelUpAttribute()
    {
        return $this->experienceNeeded - $this->experienceCollected;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'playerId', 'id');
    }

    public function getHelmetAttribute()
    {
        return $this->hasOne(EquippableItem::class, 'id', 'helmetId');
    }

    public function getRuneAttribute()
    {
        return $this->hasOne(EquippableItem::class, 'id', 'runeId');
    }

    public function getWeaponAttribute()
    {
        return $this->hasOne(EquippableItem::class, 'id', 'weaponId');
    }

    // Equippable items methods
    public function getEquippableItems()
    {
        return $this->hasMany(EquippableItem::class, 'ownerId', 'id');
    }
    public function getEquippableItemsByType($type)
    {
        return $this->hasMany(EquippableItem::class, 'ownerId', 'id')->where('type', $type);
    }
    public function getEquippableItemsByRarity($rarityId)
    {
        return $this->hasMany(EquippableItem::class, 'ownerId', 'id')->where('rarityId', $rarityId);
    }
    public function getEquippableItemsByLevel($level)
    {
        return $this->hasMany(EquippableItem::class, 'ownerId', 'id')->where('requiredLevel', '<=', $level);
    }

    // Usable items methods
    public function getUsableItems()
    {
        return $this->hasMany(UsableItem::class, 'ownerId', 'id');
    }

    public static function getPlayerToReturnById($id)
    {
        return self::find($id);
    }

}
