<?php

namespace App\Models\Enums;

enum EquippableItemType: int{
    case WEAPON = 1;
    case ARMOR = 2;
    case RUNE = 3;
}