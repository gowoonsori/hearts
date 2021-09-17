<?php

namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\User;
use App\utils\ExceptionMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    private User $user;

    public function __construct()
    {
        $this->user = new User;
    }

    /**
     * 사용자 생성
     * @param User $user
     * @return int
     * @throws InternalServerException
     */
    public function insert(User $user): int
    {
        try {
            return DB::table('users')->insertGetId([
                'name' => $user->name,
                'email' => $user->email,
                'social_id'=> $user->social_id,
            ]);
        } catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_USER_INSERT);
        }
    }

    /**
     * email 이용해 사용자 조회
     * @param $userMail
     * @return object|bool
     * @throws InternalServerException
     */
    public function findByEmail($userMail): ?object
    {
        try {
            $user = $this->user->where('email', $userMail)->first();
            if (empty($user)) {
                return false;
            }
            return $user;
        } catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_USER_GET);
        }
    }

    /**
     * id로 유저 정보 조회
     * @param $id
     * @return object|null
     * @throws InternalServerException
     */
    public function findById($id):  ?object
    {
        try {
            return $this->user->find($id);
        } catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_USER_GET);
        }
    }

    /**
     * socialId로 유저 정보 조회
     * @param $socialId
     * @return mixed
     * @throws InternalServerException
     */
    public function findBySocialId( $socialId): mixed
    {
        try {
            $user = $this->user->where(['social_id' => $socialId])->first();
            if (empty($user)) {
                Log::info('whereSocialId(): social id ' . $socialId . ' user is not found');
                return false;
            }
            return $user;
        }catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_USER_GET);
        }
    }

    /**
     * 모든 유저 정보 조회
     * @return User[]|bool|Collection
     * @throws InternalServerException
     */
    public function findAll(): Collection|array|bool
    {
        try {
            $userList = $this->user->get();
            if (empty($userList)) {
                Log::info('all(): user table is empty.');
                return false;
            }
            return $userList;
        }catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_USER_GET);
        }
    }

    /**
     * 좋아요 정보 포함한 사용자 정보 id로 조회
     * @param $id
     * @return object|null
     * @throws InternalServerException
     */
    public function findByIdWithLikes($id): ?object
    {
        try {
            return DB::table('users')->select(DB::raw("users.id, users.name,users.email, GROUP_CONCAT(likes.post_id) as likes"))
                ->leftJoin('likes', 'users.id', '=', 'likes.user_id')
                ->where('users.id', $id)
                ->groupBy('users.id')
                ->first();
        }catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_USER_GET);
        }
    }

    /**
     * 사용자의 좋아요 문구 정보 모두 조회
     * @param $id
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function findLikesById($id): \Illuminate\Support\Collection
    {
        try {
            return DB::table('posts', 'p')->select(DB::raw("p.*, u.name as owner, c.title as category"))
                ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->leftJoin('likes as l', 'p.id', '=', 'l.post_id')
                ->where('l.user_id', $id)
                ->get()
                ->transform(function ($item) {
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
        }catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * 사용자의 좋아요 문구 정보 lastId 이후의 limit 개수만큼 조회
     * @param $id
     * @param $lastId
     * @param $limit
     * @return \Illuminate\Support\Collection
     * @throws InternalServerException
     */
    public function findLikesByIdAndLastIdAndLimit($id, $lastId, $limit): \Illuminate\Support\Collection
    {
        try {
            return DB::table('posts', 'p')->select(DB::raw("p.*, u.name as owner, c.title as category"))
                ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->leftJoin('likes as l', 'p.id', '=', 'l.post_id')
                ->where('l.user_id', $id)
                ->where('p.id', '<', $lastId)
                ->orderBy('p.id', 'desc')
                ->limit($limit)
                ->get()
                ->transform(function ($item) {
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
        }catch (QueryException $exception) {
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

}
