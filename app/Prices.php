<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    use HasFactory;

    public static function findPriceByName(string $name): Prices
    {
        return Prices::where('name', $name)->first();
    }
}
