<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function cache($key, $minutes, $query, $method, ...$args)
    {
        $args = (! empty($args)) ? implode(',',$args) : null;

        if (config('project.cache') === false) {
            return $query->{$method}($args);
        }

        return Cache::remember($key, $minutes, function () use ($query, $method, $args) {
            return $query->{$method}($args);
        });
    }

    protected function cache_key($base): string
    {
        return md5($base);
    }
}
