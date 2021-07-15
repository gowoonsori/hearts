<?php


namespace App\Repositories;


use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CategoryRepository
{
    protected $category;

    public function __construct()
    {
        $this->category = new Category;
    }


    /**
     * @param $title
     * @return Category|bool|Collection
     */
    public function findByTitle($title){
        $category = $this->category->whereTitle($title)->get();
        if(empty($category)){
            return false;
        }
        return $category;
    }
    /**
     * @param $title
     * @return User|bool|Model
     */
    public function insert($title,$userId){
        $nowDt = now();

        try {
            return $this->category->create([
                'title' => $title,
                'user_id' => $userId,
                'updated_at' => $nowDt,
                'created_at' => $nowDt
            ]);
        } catch (QueryException $exception) {
            Log::error("Insert Category Fail Error Message: \n".$exception);
            throw new ModelNotFoundException("카테고리 생성중 오류가 발생했습니다.");
        }
    }

}
