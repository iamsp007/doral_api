<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends Helper
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
