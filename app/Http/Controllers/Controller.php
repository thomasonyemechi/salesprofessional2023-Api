<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    function bid()
    {
        return auth()->user()->business_id;
    }

    function generateHash($length)
    {
        return substr(str_shuffle(str_repeat('123456789', $length)), 0, $length);
    }
}
