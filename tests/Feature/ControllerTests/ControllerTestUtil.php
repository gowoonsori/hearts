<?php

namespace Tests\Feature\ControllerTests;

use App\Exceptions\UnAuthorizeException;
use App\JwtAuth;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;

trait ControllerTestUtil{

    /*session에 유저 정보 저장*/
    public function storeUserToSession($id = 1){
        $user = new User;
        $user->id = $id;
        $user->name="홍의성";

        Auth::setUser($user);
        return $id;
    }

    /*Jwt 토큰 생성*/
    /**
     * @throws UnAuthorizeException
     */
    public function createToken($id = 1): string
    {
        $user = new User;
        $user->id = $id;
        $user->name="홍의성";

        return JwtAuth::createToken($user);
    }

    public function createCookie($token): string
    {
        return Cookie::create(JwtAuth::HEADER, $token,time() + 7200);
    }


    /*카테고리와 문구 등록하는 메서드들*/
    public function createCategory( $title = '테스트 카테고리'): int
    {
        $category = $this->postJson('/user/category', ['title' => $title]);
        return json_decode($category->getContent())->response->id;
    }

    public function createPost($search = true): int
    {
        $categoryId = $this->createCategory();
        $post = $this->postJson('/user/post',[
            "content" => "문구 테스트",
            "search" => $search,
            "category_id" => $categoryId,
            "tags" => [
                ["tag" =>"테스트 태그1", "color" => 352],
                ["tag" => "테스트 태그2", "color" => 342],
            ]
        ]);
        return json_decode($post->getContent())->response->id;
    }

    public function createPostWithCategoryId($categoryId): int
    {
        $post = $this->postJson('/user/post',[
            "content" => "문구 테스트",
            "search" => true,
            "category_id" => $categoryId,
            "tags" => [
                ["tag" =>"테스트 태그1", "color" => 352],
                ["tag" => "테스트 태그2", "color" => 342],
            ]
        ]);
        return json_decode($post->getContent())->response->id;
    }

    /**
     * @param int $postId
     * @return void
     * */
    public function likePost(int $postId) {
        $this->patchJson('/user/post/' . $postId . '/like');
    }
}
