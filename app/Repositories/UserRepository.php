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
    protected $user;

    public function __construct()
    {
        $this->user = new User;
    }

    /**
     * @param $socialData
     * @return User|Model
     * @throws InternalServerException
     */
    public function insert($socialData){
        $nowDt = now();

        try {
            return $this->user->create([
                'name' => $socialData->getName(),
                'email' => $socialData->getEmail(),
                'social_id' => $socialData->getId(),
                'access_token' => $socialData->token,
                'updated_at' => $nowDt,
                'created_at' => $nowDt
            ]);
        } catch (QueryException $exception) {
            Log::error("Sign Up Fail Error Message: \n".$exception);
            throw new InternalServerException("사용자 등록중 오류가 발생했습니다.");
        }
    }

    /**
     * @param $userMail
     * @return User|bool|Collection
     */
    public function findByEmail($userMail){
        $user = $this->user->where('email', $userMail)->first();
        if(empty($user)){
            return false;
        }
        return $user;
    }

    /**
     * Obtain the user information by data table idx.
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
     * @param $githubId
     * @return mixed
     */
    public function findBySocialId( $socialId){
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
    public function findAll(){
        $userList = $this->user->get();
        if(empty($userList)){
            Log::info('all(): user table is empty.');
            return false;
        }
        return $userList;
    }
}
