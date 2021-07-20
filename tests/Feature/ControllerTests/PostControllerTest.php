<?php

namespace Tests\Feature\ControllerTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    /**
     * 문구 id로 문구 조회 성공 테스트
     * @test
     * @return void
     */
    public function getPostByPostIdSuccessTest()
    {
        $userId = 1;
        $postId = 1;
        $response = $this->getJson('/user/' . $userId . '/post?postId=' . $postId);
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.content', "문구 샘플1")
            ->assertJsonPath('response.search', 0)
            ->assertJsonPath('response.category_id', 1)
            ->assertJsonPath('response.user_id', 1)
            ->assertJsonStructure([
                'success','response' => [
                    'id','content','total_like','share_cnt','visit_cnt','search','created_at',
                    'updated_at','user_id','category_id','tags'
                ]
            ]);
    }


    /**
     * 문구ID로 조회 시 실패 테스트 | 없는 문구id
     * @test
     * @return void
     */
    public function getPostByPostIdFailTestNotExistId()
    {
        $userId = 1;
        $postId = rand() . rand(0,1000);

        $response = $this->getJson('/user/' . $userId . '/post?postId=' . $postId);
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message',"존재하지 않은 문구입니다.");
    }

    /**
     * 문구ID로 조회 시 실패 테스트 | 검색 제한 걸려있는 문구인데 자기 문구 아닌경우
     * @test
     * @return void
     */
    public function getPostByPostIdFailTestNotSearchPost()
    {
        $userId = 10;
        $postId = 1;

        $response = $this->getJson('/user/' . $userId . '/post?postId=' . $postId);
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400)
            ->assertJsonPath('response.message',"조회할 수 없는 문구 입니다.");
    }

    /**
     * 문구 등록 성공 테스트
     * @test
     * @return void
     */
    public function createPostSuccessTest()
    {
        $userId = 1;
        $response = $this->postJson('/user/' . $userId . '/post',[
            "content" => "문구 샘플2",
            "search" => true,
            "category_id" => 1,
            "tags" => [
                "마우스"
            ]
        ]);
        $response->dump();
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.content', "문구 샘플2")
            ->assertJsonPath('response.search', true)
            ->assertJsonPath('response.category_id', 1)
            ->assertJsonPath('response.user_id', 1)
            ->assertJsonPath('response.total_like', 0)
            ->assertJsonPath('response.share_cnt', 0)
            ->assertJsonPath('response.visit_cnt', 0)
            ->assertSee('id')
            ->assertSee('tags');
    }

    /**
     * 문구 등록 실패 테스트 | 잘못된 양식
     * @test
     * @return void
     */
    public function createPostFailTest()
    {
        $userId = 1;
        $response = $this->postJson('/user/' . $userId . '/post',[
            "content" => "문구 샘플2",
            "search" => true
        ]);
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status', 404)
            ->assertSee('message' );
    }

    /**
     * 내 모든 문구 조회 성공 테스트
     * @test
     * @return void
     */
    public function getPostsSuccessTest()
    {
        $userId = 1;
        $response = $this->getJson('/user/' . $userId . '/post/all');
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure(['success',
                'response'=> [
                    '0' => [
                        'id',
                        'content',
                        'total_like',
                        'share_cnt',
                        'visit_cnt',
                        'search',
                        'created_at',
                        'updated_at',
                        'user_id',
                        'category_id',
                        'tags']
            ]]);
    }

    /**
     * 내 모든 문구 조회 성공 테스트 | 문구 없는 경우
     * @test
     * @return void
     */
    public function getPostsSuccessTestNull()
    {
        $userId = 12345678;
        $response = $this->getJson('/user/' . $userId . '/post/all');
        $response->dump();
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response', null);
    }

    /**
     * 내 모든 문구 조회 실패 테스트 | 없는 userid
     * @test
     * @return void
     */
    public function getPostsFailTest()
    {
        $userId = 132421;
        $response = $this->getJson('/user/' . $userId . '/post/all');
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 사용자입니다.');
    }

    /**
     * 카테고리 별 내 모든 문구 조회 성공 테스트
     * @test
     * @return void
     */
    public function getPostsByCategorySuccessTest()
    {
        $userId = 1;
        $categoryId = 1;
        $response = $this->getJson('/user/' . $userId . '/post/category/' . $categoryId);
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure(['success',
                'response'=> [
                    '0' => [
                        'id',
                        'content',
                        'total_like',
                        'share_cnt',
                        'visit_cnt',
                        'search',
                        'created_at',
                        'updated_at',
                        'user_id',
                        'category_id',
                        'tags']
                ]]);
    }

    /**
     * 내 모든 문구 조회 실패 테스트
     * @test
     * @return void
     */
    public function getPostsByCategorySuccessTestNull()
    {
        $userId = 1;
        $categoryId = 12;
        $response = $this->getJson('/user/' . $userId . '/post/category/' . $categoryId);
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response',null);

    }

    /**
     * 문구 공유횟수 증가 성공 테스트
     * @test
     * @return void
     */
    public function updateShareCountSuccessTest()
    {
        $postId = 1;
        $response = $this->patchJson( '/post/' . $postId . '/share');
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure(['success',
                'response'=> [
                        'id',
                        'content',
                        'total_like',
                        'share_cnt',
                        'visit_cnt',
                        'search',
                        'created_at',
                        'updated_at',
                        'user_id',
                        'category_id',
                        'tags']
                ]);
    }

    /**
     * 문구 공유횟수 증가 실패 테스트 | 없는 문구
     * @test
     * @return void
     */
    public function updateShareCountFailTest()
    {
        $postId = 2;
        $response = $this->patchJson('/post/' . $postId . '/share');
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 문구입니다.');
    }
}
