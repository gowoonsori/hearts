<?php
namespace App\Repositories;

use App\Models\badge;

class BadgeRepository implements BadgeRepositoryInterface
{
    protected $badge;
    /**
     * ReportRepository constructor.
     */
    public function __construct()
    {
        $this->badge = new badge();
    }

    /**
     * Obtain the report information from report data table.
     *
     * @param $userIdx
     * @param $type
     * @return bool
     */
    public function get($userIdx, $type)
    {

    }

}
