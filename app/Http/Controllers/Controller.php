<?php

namespace App\Http\Controllers;

use App\Traits\AfricasTalkingSMS;
use App\Traits\ApiResponser;
use App\Traits\DateConverter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ApiResponser,AuthorizesRequests, DispatchesJobs, ValidatesRequests,DateConverter,AfricasTalkingSMS;
}
