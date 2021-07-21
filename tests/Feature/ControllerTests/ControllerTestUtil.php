<?php

namespace Tests\Feature\ControllerTests;

trait ControllerTestUtil{
    /*카테고리와 문구 등록하는 메서드들*/
    public function createCategory($userId, $title = '테스트 카테고리'): int
    {
        $category = $this->postJson('/user/' . $userId . '/category', ['title' => $title]);
        return json_decode($category->getContent())->response->id;
    }

    public function createPost($userId, $search = true): int
    {
        $categoryId = $this->createCategory($userId);
        $post = $this->postJson('/user/' . $userId . '/post',[
            "content" => "문구 테스트",
            "search" => $search,
            "category_id" => $categoryId,
            "tags" => [
                "테스트 태그1", "테스트 태그2"
            ]
        ]);
        return json_decode($post->getContent())->response->id;
    }

    public function createPostWithCategoryId($userId,$categoryId): int
    {
        $post = $this->postJson('/user/' . $userId . '/post',[
            "content" => "문구 테스트",
            "search" => true,
            "category_id" => $categoryId,
            "tags" => [
                "테스트 태그1", "테스트 태그2"
            ]
        ]);
        return json_decode($post->getContent())->response->id;
    }
}
