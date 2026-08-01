<?php

namespace App\Http\Controllers;

abstract class Controller
{
    use \App\Http\Concerns\ApiResponses;
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
}
