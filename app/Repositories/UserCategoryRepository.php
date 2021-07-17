<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;

class UserCategoryRepository
{
    /**
     * 내가 가진 카테고리인지 확인
     * @param integer $userId
     * @param integer $categoryId
     * @return Model | Object | null
     */
    function haveCategory(int $userId, int $categoryId)
    {
        return DB::table('user_category')->where([
            'user_id' => $userId,
            'category_id' => $categoryId,
        ])->first();
    }
}
