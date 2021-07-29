<?php

namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\User;
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
            Log::error("Sign Up Fail Error Message: \n".$exception);
            throw new InternalServerException("사용자 등록중 오류가 발생했습니다.");
        }
    }

    /**
     * @param $userMail
     * @return User|bool
     */
    public function findByEmail($userMail): User|bool
    {
        $user = $this->user->where('email', $userMail)->first();
        if(empty($user)){
            return false;
        }
        return $user;
    }

    /**
     * Obtain the user information by data table id
     * @param $id
     * @return User|bool|Collection|Model
     */
    public function findById($id){
        $user = $this->user->find($id);
        if(empty($user)){
            Log::info('whereId(): id '.$id.' user is not found');
            return false;
        }
        return $user;
    }

    /**
     * Obtain the user information by User's gitub id.
     *
     * @param $socialId
     * @return mixed
     */
    public function findBySocialId( $socialId): mixed
    {
        $user = $this->user->where(['social_id' =>  $socialId])->first();
        if(empty($user)){
            Log::info('whereSocialId(): social id '. $socialId.' user is not found');
            return false;
        }
        return $user;
    }

    /**
     * Obtain all user's information in random order
     *
     * @return User[]|bool|Collection
     */
    public function findAll(): Collection|array|bool
    {
        $userList = $this->user->get();
        if(empty($userList)){
            Log::info('all(): user table is empty.');
            return false;
        }
        return $userList;
    }

    public function findByIdWithLikes($id)
    {
        return DB::table('users')->select(DB::raw("users.id, users.name,users.email, GROUP_CONCAT(likes.post_id) as likes"))
            ->leftJoin('likes','users.id','=','likes.user_id')
            ->where('users.id', $id)
            ->groupBy('users.id')
            ->first();
    }

    public function findLikesById($id): \Illuminate\Support\Collection
    {
        return DB::table('posts','p')->select(DB::raw("p.*, u.name as owner, c.title as category"))
            ->leftJoin('users as u','u.id','=','p.user_id')
            ->leftJoin('categories as c','c.id','=','p.category_id')
            ->leftJoin('likes as l','p.id','=','l.post_id')
            ->where('l.user_id', $id)
            ->get();
    }

}
