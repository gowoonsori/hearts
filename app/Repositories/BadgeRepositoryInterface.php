<?php

namespace App\Repositories;


interface ReportRepositoryInterface
{
    /**
     * @param $url
     * @return mixed
     */
    public function get($url);
}
