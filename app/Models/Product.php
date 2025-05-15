<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // required products
    protected $product = 'products';

    protected $fillable = [

        'name',
        'description',
        'price'


    ];
}
