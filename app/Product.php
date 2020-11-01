<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [ 'uniq_id', 'name', 'description', 'price', 'category_id', 'subcategory_id', 'status'];
}
