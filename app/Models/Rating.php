<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Rating extends Model
{
    protected $guarded = [];
    protected $dates = ['created_at'];

}
