<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use ApiResponses;
    use AuthorizesRequests;
}
