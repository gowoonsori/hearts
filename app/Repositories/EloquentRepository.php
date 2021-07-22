<?php


namespace App\Repositories;


interface EloquentRepository{
    /**
     * @param $idx
     * @return mixed
     */
    public function findById($id);

    /**
     * @return mixed
     */
    public function findAll();

}
