<?php

namespace Tests\Feature\ControllerTests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ControllerTestUtil{

    /*session에 유저 정보 저장*/
    public function storeUserToSession($id = 1){
        $user = new User;
        $user->id = $id;
        $user->name="홍의성";

        Auth::setUser($user);
        return $id;
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
                "테스트 태그1", "테스트 태그2"
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
                "테스트 태그1", "테스트 태그2"
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
