<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeartsController extends Controller
{
    function show($request){
        $url = $request->query('url');
    }
}
