<?php


namespace App\Repositories;


use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryRepository
{
    protected $category;

    public function __construct()
    {
        $this->category = new Category;
    }


    /**
     * 문구로 검색
     * @param $title
     * @return Category| null
     */
    public function findByTitle($title): ?Category
    {
        return $this->category->where(['title' => $title])->first();
    }

    /**
     * id로 문구 검색
     * @param $id
     * @return Category|null
     */
    public function findById($id): ?Category
    {
        return $this->category->where(['id' => $id])->first();
    }



    /**
     * 카테고리 삽입
     * @param $title
     * @return User|bool|Model
     */
    public function insert($title){
        $nowDt = now();

        try {
            return $this->category->create([
                'title' => $title,
                'updated_at' => $nowDt,
                'created_at' => $nowDt
            ]);
        } catch (QueryException $exception) {
            Log::error("Insert Category Fail Error Message: \n".$exception);
            throw new ModelNotFoundException("카테고리 생성중 오류가 발생했습니다.");
        }
    }
}
