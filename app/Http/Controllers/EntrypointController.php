<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntrypointController extends Controller
{
    public function index()
    {
        return redirect('/login');
    }
}
