<?php


namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\utils\ExceptionMessage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserCategoryRepository
{

    /**
     * 사용자 id를 이용해 user-category 와 category 테이블의 row 조회
     * @param integer $userId
     * @return object|null
     * @throws InternalServerException
     */
    function findByUserId(int $userId):  ?object
    {
        try {
            return DB::table('user_category','uc')->select('uc.*, c.title')
                ->join('categories as c', 'c.id', '=', 'uc.category_id' )
                ->where(['uc.user_id' => $userId])
                ->get();
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_GET);
        }
    }

    /**
     * fk를 이용해 user-category sub 테이블의 row 한개 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return object|null
     * @throws InternalServerException
     */
    function findByUserIdAndCategoryId(int $userId, int $categoryId):  ?object
    {
        try {
            return DB::table('user_category')->where([
                'user_id' => $userId,
                'category_id' => $categoryId,
            ])->first();
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_GET);
        }
    }

    /**
     * id를 이용하여 row 삭제
     * @param int $id
     * @return void
     * @throws InternalServerException
     */
    function deleteById(int $id): void
    {
        try {
            DB::table('user_category')->delete(['id' => $id,]);
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_DELETE);
        }
    }

    /**
     * 삽입
     * @param int $categoryId
     * @param int $userId
     * @return bool
     * @throws InternalServerException
     */
    function insert(int $categoryId, int $userId): bool
    {
        try {
            return DB::table('user_category')->insert([
                'category_id' => $categoryId,
                'user_id' => $userId
            ]);
        }catch (QueryException $e){
            Log::error($e);
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_INSERT);
        }
    }

    /**
     * 삽입
     * @param int $userId
     * @param int $categoryId
     * @param int $updateId
     * @return int
     * @throws InternalServerException
     */
    function update(int $userId, int $categoryId, int $updateId): int
    {
        try {
            return DB::table('user_category')
                ->where('user_id', $userId)
                ->where('category_id',$categoryId)
                ->update([
                    'category_id' => $updateId,
                ]);
        }catch (QueryException $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_CATEGORY_UPDATE);
        }
    }
}
