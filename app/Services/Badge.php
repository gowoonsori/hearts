<?php


namespace App\Services;


interface  Badge{
    function generate($edgeRound, $bgColor, $iconColor, $heatsColor);
}
