<?php

namespace Tests\Feature\ControllerTests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use DatabaseTransactions,ControllerTestUtil;

    /**
     * 문구 id로 문구 조회 성공 테스트
     * @test
     * @return void
     */
    public function getPostByPostIdSuccessTest()
    {
        //given
        $userId = 1;
        $postId = $this->createPost($userId);

        //when
        $response = $this->getJson('/user/' . $userId . '/post?postId=' . $postId);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.content', "문구 테스트")
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
        //given
        $userId = 1;
        $postId = rand() . rand(0,1000);

        //when
        $response = $this->getJson('/user/' . $userId . '/post?postId=' . $postId);

        //then
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
        //given
        $userId = 10;
        $createUserId = 1;
        $postId = $this->createPost($createUserId,false);

        //when
        $response = $this->getJson('/user/' . $userId . '/post?postId=' . $postId);

        //then
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
        //given
        $userId = 1;
        $categoryId = $this->createCategory($userId);

        //when
        $response = $this->postJson('/user/' . $userId . '/post',[
            "content" => "문구 샘플2",
            "search" => true,
            "category_id" => $categoryId,
            "tags" => [
                "마우스"
            ]
        ]);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.content', "문구 샘플2")
            ->assertJsonPath('response.search', true)
            ->assertJsonPath('response.category_id', $categoryId)
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
        //given
        $userId = 1;

        //when
        $response = $this->postJson('/user/' . $userId . '/post',[
            "content" => "문구 샘플2",
            "search" => true
        ]);

        //then
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
        //given
        $userId = 1;
        $this->createPost($userId);

        //when
        $response = $this->getJson('/user/' . $userId . '/post/all');

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure(['success',
                'response'=> [
                    '0' => [
                        'id', 'content', 'total_like', 'share_cnt', 'visit_cnt', 'search',
                        'created_at', 'updated_at', 'user_id', 'category_id', 'tags'
                    ]
            ]]);
    }

    /**
     * 내 모든 문구 조회 성공 테스트 | 문구 없는 경우
     * @test
     * @return void
     */
    public function getPostsSuccessTestNull()
    {
        //given
        $userId = 12345678;

        //when
        $response = $this->getJson('/user/' . $userId . '/post/all');

        //then
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
        //given
        $userId = 132421;

        //when
        $response = $this->getJson('/user/' . $userId . '/post/all');

        //then
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
        //given
        $userId = 1;
        $categoryId = $this->createCategory($userId);
        $this->createPostWithCategoryId($userId,$categoryId);

        //when
        $response = $this->getJson('/user/' . $userId . '/post/category/' . $categoryId);

//      //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure(['success',
                'response'=> [
                    '0' => [
                        'id', 'content', 'total_like', 'share_cnt', 'visit_cnt', 'search',
                        'created_at', 'updated_at', 'user_id', 'category_id', 'tags'
                    ]
                ]]);
    }

    /**
     * 내 모든 문구 조회 실패 테스트
     * @test
     * @return void
     */
    public function getPostsByCategorySuccessTestNull()
    {
        //given
        $userId = 1;
        $categoryId = 12;

        //when
        $response = $this->getJson('/user/' . $userId . '/post/category/' . $categoryId);

        //then
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
        //given
        $userId = 1;
        $postId = $this->createPost($userId);

        //when
        $response = $this->patchJson( '/post/' . $postId . '/share');

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.share_cnt', 1)
            ->assertJsonStructure(['success',
                'response'=> [
                        'id', 'content', 'total_like', 'share_cnt', 'visit_cnt', 'search',
                        'created_at', 'updated_at', 'user_id', 'category_id', 'tags'
                ]
            ]);
    }

    /**
     * 문구 공유횟수 증가 실패 테스트 | 없는 문구
     * @test
     * @return void
     */
    public function updateShareCountFailTest()
    {
        //given
        $postId = 2;

        //when
        $response = $this->patchJson('/post/' . $postId . '/share');

        //then
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 문구입니다.');
    }

    /**
     * 문구 삭제 성공 테스트
     * @test
     * @return void
     */
    public function deletePostSuccessTest()
    {
        //given
        $userId = 1;
        $postId = $this->createPost($userId);

        //when
        $response = $this->deleteJson('/user/' . $userId . '/post?postId=' . $postId);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response',true)
            ->assertJsonStructure(['success', 'response']);
        $this->getJson('/user/' . $userId . '/post?postId=' . $postId)
            ->assertStatus(404)
            ->assertJsonPath('response.message','존재하지 않은 문구입니다.');
    }

    /**
     * 문구 삭제 실패 테스트 | 쿼리 파라미터 존재 x
     * @test
     * @return void
     */
    public function deletePostFailTestNotExistQueryParameter()
    {
        //given
        $userId = 1;
        $postId = $this->createPost($userId);

        //when
        $response = $this->deleteJson('/user/' . $userId . '/post?');

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400)
            ->assertJsonPath('response.message','잘못된 요청입니다.');
    }

    /**
     * 문구 삭제 실패 테스트 | 자기 문구가 아닌경우
     * @test
     * @return void
     */
    public function deletePostFailTestForbidden()
    {
        //given
        $createUserId = 1;
        $userId = 13231231;
        $postId = $this->createPost($createUserId);

        //when
        $response = $this->deleteJson('/user/' . $userId . '/post?postId=' . $postId);

        //then
        $response->assertStatus(403)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',403)
            ->assertJsonPath('response.message','잘못된 접근입니다.');
    }
}
