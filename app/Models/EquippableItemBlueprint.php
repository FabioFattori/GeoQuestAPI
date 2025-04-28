<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquippableItemBlueprint extends Model
{
    use HasFactory;
    protected $table = 'equippableItemBlueprints'; 
    protected $fillable = [
        'name',
        'description',
        'type',
        'imagePath',
        'baseDamage',
        'baseHealth',
        'requiredLevel',
    ];

    protected $casts = [
        'type' => 'integer',
        'baseDamage' => 'integer',
        'baseHealth' => 'integer',
    ];

    public function getEquippableItems()
    {
        return $this->hasMany(EquippableItem::class, 'blueprintId', 'id');
    }

    public static function getPossibleBlueprintsGivenLevel($level)
    {
        return EquippableItemBlueprint::where('requiredLevel', '<=', $level)->get();
    }

    public static function createEquippableItem($rarityId,$blueprintId,$ownerId = null)
    {
        $equippableItem = new EquippableItem();
        $equippableItem->rarityId = $rarityId;
        $equippableItem->blueprintId = $blueprintId;
        if($ownerId){        
            $equippableItem->ownerId = $ownerId;
        }
        $equippableItem->save();

        return $equippableItem;
    }


}
