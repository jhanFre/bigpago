<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
    	$this->middleware('auth:api');
    }
}
