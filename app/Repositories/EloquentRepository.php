<?php


namespace App\Repositories;


interface ElequentRepository{
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
