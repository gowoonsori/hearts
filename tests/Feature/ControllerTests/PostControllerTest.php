<?php

namespace Tests\Feature\ControllerTests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
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
        $userId = $this->storeUserToSession();
        $postId = $this->createPost($userId);

        //when
        $response = $this->getJson('/user/post?postId=' . $postId);
        $response->dump();

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
        $userId = $this->storeUserToSession();
        $postId = rand() . rand(0,1000);

        //when
        $response = $this->getJson('/user/post?postId=' . $postId);

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
        $createUserId = $this->storeUserToSession();
        $postId = $this->createPost(false);

        //when
        Auth::logout();
        Auth::setUser(new User(['id' => $userId, 'name' => '테스트']));
        $response = $this->getJson('/user/post?postId=' . $postId);

        //then
        $response->dump();
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
        $userId = $this->storeUserToSession();
        $categoryId = $this->createCategory($userId);

        //when
        $response = $this->postJson('/user/post',[
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
        $userId = $this->storeUserToSession();

        //when
        $response = $this->postJson('/user/post',[
            "content" => "문구 샘플2",
            "search" => true
        ]);

        //then
        $response->dump();
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status', 400)
            ->assertSee('message','잘못된 요청입니다.' );
    }

    /**
     * 내 모든 문구 조회 성공 테스트
     * @test
     * @return void
     */
    public function getPostsSuccessTest()
    {
        //given
        $userId = $this->storeUserToSession();
        $this->createPost($userId);

        //when
        $response = $this->getJson('/user/post/all');

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
        $userId = $this->storeUserToSession(12345678);

        //when
        $response = $this->getJson('/user/post/all');

        //then
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
        //when
        $response = $this->getJson('/user/post/all');

        //then
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','Attempt to read property "id" on null');
    }

    /**
     * 카테고리 별 내 모든 문구 조회 성공 테스트
     * @test
     * @return void
     */
    public function getPostsByCategorySuccessTest()
    {
        //given
        $userId = $this->storeUserToSession();
        $categoryId = $this->createCategory($userId);
        $this->createPostWithCategoryId($categoryId);

        //when
        $response = $this->getJson('/user/post/category/' . $categoryId);

//      //then
        $response->dump();
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
        $userId = $this->storeUserToSession();
        $categoryId = 12;

        //when
        $response = $this->getJson('/user/post/category/' . $categoryId);

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
        $userId = $this->storeUserToSession();
        $postId = $this->createPost($userId);

        //when
        $response = $this->patchJson( '/post/' . $postId . '/share');

        //then
        $response->dump();
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
        $this->storeUserToSession();
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
        $userId = $this->storeUserToSession();
        $postId = $this->createPost($userId);

        //when
        $response = $this->deleteJson('/user/post?postId=' . $postId);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response',true)
            ->assertJsonStructure(['success', 'response']);
        $this->getJson('/user/post?postId=' . $postId)
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
        $userId = $this->storeUserToSession();
        $postId = $this->createPost($userId);

        //when
        $response = $this->deleteJson('/user/post?');

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
        $createUserId = $this->storeUserToSession();
        $userId = 13231231;
        $postId = $this->createPost($createUserId);

        //when
        Auth::logout();
        Auth::setUser(new User(['id'=>$userId, 'name' => '테스트']));
        $response = $this->deleteJson('/user/post?postId=' . $postId);

        //then
        $response->assertStatus(403)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',403)
            ->assertJsonPath('response.message','잘못된 접근입니다.');
    }

    /**
     * 문구 등록 수정 테스트
     * @test
     * @return void
     */
    public function updatePostSuccessTest()
    {
        //given
        $userId = $this->storeUserToSession();
        $categoryId = $this->createCategory($userId);
        $postId = $this->createPostWithCategoryId($categoryId);

        //when
        $response = $this->patchJson('/user/post?postId=' . $postId,
            [
            "content" => "수정 문구",
            "search" => true,
            "category_id" => $categoryId,
            "tags" => [
                "수정 태그"
            ]
        ]);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure([
                'success',
                'response'=> [
                    'id', 'content', 'total_like', 'share_cnt', 'visit_cnt', 'search',
                    'created_at', 'updated_at', 'user_id', 'category_id', 'tags'
                ]
            ]);
        $this->getJson('/user/post?postId=' . $postId)
            ->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.content', "수정 문구")
            ->assertJsonPath('response.search', 1)
            ->assertJsonPath('response.category_id', $categoryId)
            ->assertJsonPath('response.user_id', 1)
            ->assertJsonPath('response.total_like', 0)
            ->assertJsonPath('response.share_cnt', 0)
            ->assertJsonPath('response.visit_cnt', 0)
            ->assertJsonPath('response.tags', ["수정 태그"]);
    }
}
