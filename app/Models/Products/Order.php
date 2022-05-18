<?php

namespace App\Models\Products;

use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    protected $guarded=[];
    protected $dates = ['placedOn'];

}
