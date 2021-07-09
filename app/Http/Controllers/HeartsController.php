<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HeartsController extends Controller
{
    function show(Request $request){
        $url = $request->input('url');
        $edgeRound = $request->input('edge_round');
        $bgColor = $request->input('bg_color');
        $iconColor = $request->input('icon_color');
        $heatsColor = $request->input('hearts_color');
        $vertical = $request->input('vertical');

        $badgePath = 'public/badge.svg';
        if(Storage::exists($badgePath)){
            return Storage::get($badgePath);
        }
    }
}
