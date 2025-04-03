<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquippableItemBlueprint extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'baseDamage',
        'baseHealth',
    ];

    protected $casts = [
        'type' => EquippableItemType::class,
        'baseDamage' => 'integer',
        'baseHealth' => 'integer',
    ];

    public function getEquippableItems()
    {
        return $this->hasMany(EquippableItem::class, 'blueprintId', 'id');
    }

    public static function createEquippableItem($rarityId,$blueprintId,$ownerId)
    {
        $equippableItem = new EquippableItem();
        $equippableItem->rarityId = $rarityId;
        $equippableItem->blueprintId = $blueprintId;
        $equippableItem->ownerId = $ownerId;
        $equippableItem->save();

        return $equippableItem;
    }


}
