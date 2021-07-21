<?php


namespace ControllerTests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * 문구 좋아요 성공 테스트
     * @test
     * @return void
     */
    public function likePostSuccessTest()
    {
        $userId = 1;
        $postId = 1;
        $response = $this->patchJson('/user/' . $userId . '/post/' . $postId . '/like');
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure([
                'success',
                'response' => [
                    'id','content','total_like','share_cnt','visit_cnt','search','created_at',
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
        $userId = 1;
        $response = $this->getJson('/user/' . $userId . '/post/like');
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure([
                "success",
                "response" => [
                    '0'=> [
                        'id','content','total_like','share_cnt','visit_cnt',
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
        $userId = 1;
        $postId = 1;
        $response = $this->patchJson('/user/' . $userId . '/post/' . $postId . '/like');
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
        $userId = 1;
        $postId = 1234123541231;
        $response = $this->patchJson('/user/' . $userId . '/post/' . $postId . '/like');
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
        $userId = 12345678;
        $postId = 1;
        $response = $this->patchJson('/user/' . $userId . '/post/' . $postId . '/like');
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
        $userId = 1;
        $postId = 1;
        $response = $this->deleteJson('/user/' . $userId . '/post/' . $postId . '/like');
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response',true);
    }

    /**
     * 문구 좋아요 취소 실패 테스트 | 좋아요 하지 않은 글
     * @test
     * @return void
     */
    public function unlikePostFailTestNotLike()
    {
        $userId = 1;
        $postId = 1;
        $response = $this->deleteJson('/user/' . $userId . '/post/' . $postId . '/like');
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
        $userId = 1;
        $postId = 1234123541231;
        $response = $this->patchJson('/user/' . $userId . '/post/' . $postId . '/like');
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 문구입니다.');
    }


}
