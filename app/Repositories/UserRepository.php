<?php

namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
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
     * @return bool
     * @throws InternalServerException
     */
    public function insert(User $user): bool
    {
        try {
            return $user->save();
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
}
