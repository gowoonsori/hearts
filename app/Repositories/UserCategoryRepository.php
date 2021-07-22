<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;

class UserCategoryRepository
{
    /**
     * fk를 이용해 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return Model | Object | null
     */
    function findByUserIdAndCategoryId(int $userId, int $categoryId)
    {
        return DB::table('user_category')->where([
            'user_id' => $userId,
            'category_id' => $categoryId,
        ])->first();
    }

    /**
     * 삭제
     * @param int $id
     * @return void
     */
    function deleteById(int $id): void
    {
        DB::table('user_category')->delete(['id' => $id,]);
    }

    /**
     * 삽입
     * @param int $categoryId
     * @param int $userId
     * @return bool
     */
    function insert(int $categoryId, int $userId): bool
    {
        return DB::table('user_category')->insert([
            'category_id' => $categoryId,
            'user_id' => $userId
        ]);
    }

    /**
     * 삽입
     * @param int $id
     * @param int $categoryId
     * @return mixed
     */
    function update(int $id, int $categoryId)
    {
        return DB::table('user_category')
            ->where('id',$id)
            ->update([
            'category_id' => $categoryId,
        ]);
    }
}
