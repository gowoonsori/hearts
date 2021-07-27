<?php


namespace ControllerTests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\ControllerTests\ControllerTestUtil;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    use DatabaseTransactions,ControllerTestUtil;


    /**
     * 문구 좋아요 성공 테스트
     * @test
     * @return void
     */
    public function likePostSuccessTest()
    {
        //when
        $userId =  $this->storeUserToSession();
        $postId = $this->createPost($userId);

        //when
        $response = $this->patchJson('/user/post/' . $postId . '/like');

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure([
                'success',
                'response' => [
                    'id','content','total_like','share_cnt','search','created_at',
                    'updated_at','user_id','category_id','tags'
                ]
            ]);
    }

    /**
     * 내가 좋아요한 문구 검색 성공 테스트
     * @test
     * @return void
     */
    public function getMyLikePostsSuccessTest()
    {
        //given
        $userId =  $this->storeUserToSession();
        $categoryId = $this->createCategory();
        $postId = $this->createPostWithCategoryId($categoryId);
        $this->likePost($postId);
        $postId = $this->createPostWithCategoryId($categoryId);
        $this->likePost($postId);

        //when
        $response = $this->getJson('/user/post/like');

        //then
        $response->dump();
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure([
                "success",
                "response" => [
                    '0'=> [
                        'id','content','total_like','share_cnt',
                        'search','created_at','updated_at','user_id','category_id','tags'
                    ]
                ]
            ]);
    }

    /**
     * 문구 좋아요 실패 테스트 | 이미 좋아요 한 문구
     * @test
     * @return void
     */
    public function likePostFailTestDuplicate()
    {
        //given
        $userId =  $this->storeUserToSession();
        $postId = $this->createPost($userId);
        $this->likePost($postId);

        //when
        $response = $this->patchJson('/user/post/' . $postId . '/like');

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400)
            ->assertJsonPath('response.message','이미 좋아요한 글 입니다.');
    }

    /**
     * 문구 좋아요 실패 테스트 | 없는 문구
     * @test
     * @return void
     */
    public function likePostFailTestNotExistPost()
    {
        //given
        $userId = $this->storeUserToSession();
        $postId = 1234123541231;

        //when
        $response = $this->patchJson('/user/post/' . $postId . '/like');

        //then
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 문구입니다.');
    }

    /**
     * 문구 좋아요 실패 테스트 | 검색할 수 없는 문구
     * @test
     * @return void
     */
    public function likePostFailTestImpossibleSearch()
    {
        //given
        $postUserId = $this->storeUserToSession();
        $postId = $this->createPost(false);
        Auth::logout();

        $userId = $this->storeUserToSession(12345678);

        //when
        $response = $this->patchJson('/user/post/' . $postId . '/like');

        //then
        $response->dump();
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400)
            ->assertJsonPath('response.message','잘못된 요청입니다.');
    }

    /**
     * 문구 좋아요 취소 성공 테스트
     * @test
     * @return void
     */
    public function unlikePostSuccessTest()
    {
        //given
        $userId = $this->storeUserToSession();
        $postId = $this->createPost($userId);
        $this->likePost($postId);

        //when
        $response = $this->deleteJson('/user/post/' . $postId . '/like');

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure([
                'success',
                'response' => [
                    'id','content','total_like','share_cnt','search','created_at',
                    'updated_at','user_id','category_id','tags'
                ]
            ]);
    }

    /**
     * 문구 좋아요 취소 실패 테스트 | 좋아요 하지 않은 글
     * @test
     * @return void
     */
    public function unlikePostFailTestNotLike()
    {
        //given
        $userId = $this->storeUserToSession();
        $postId = $this->createPost($userId);

        //when
        $response = $this->deleteJson('/user/post/' . $postId . '/like');

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400)
            ->assertJsonPath('response.message','좋아요 하지 않은 글입니다.');
    }

    /**
     * 문구 좋아요 실패 테스트 | 없는 문구
     * @test
     * @return void
     */
    public function unlikePostFailTestNotExistPost()
    {
        //given
        $userId = $this->storeUserToSession();
        $postId = 1234123541231;

        //when
        $response = $this->patchJson('/user/post/' . $postId . '/like');

        //then
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 문구입니다.');
    }
}
