<?php


namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\Category;
use App\utils\ExceptionMessage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CategoryRepository
{
    protected Category $category;

    public function __construct()
    {
        $this->category = new Category;
    }

    /**
     * 카테고리 이름으로 조회
     * @param $title
     * @return object| null
     * @throws InternalServerException
     */
    public function findByTitle($title):  ?object
    {
        try {
            return $this->category->where(['title' => $title])->first();
        }catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_GET);
        }
    }

    /**
     * id로 카테고리 조회
     * @param $id
     * @return object|null
     * @throws InternalServerException
     */
    public function findById($id):  ?object
    {
        try {
            return $this->category->where(['id' => $id])->first();
        }catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_GET);
        }
    }


    /**
     * 카테고리 삽입
     * @param $title
     * @return object|null
     * @throws InternalServerException
     */
    public function insert($title):  ?object
    {
        $nowDt = now();
        try {
            return $this->category->create([
                'title' => $title,
                'updated_at' => $nowDt,
                'created_at' => $nowDt
            ]);
        } catch (QueryException $e) {
            Log::error($e);
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_INSERT);
        }
    }
}
