<?php


namespace Tests\Feature\ControllerTests;

use App\Exceptions\UnAuthorizeException;
use App\JwtAuth;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseTransactions,WithoutMiddleware,ControllerTestUtil;

    /**
     * 사용자의 카테고리 모두 조회 성공 테스트 (기본 카테고리만 있을때)
     * @test
     * @return void
     */
    public function getUserCategoriesSuccessNoCategory()
    {
        //given
        $userId = $this->storeUserToSession(1);

        //when
        $response = $this->getJson('/user/category');

        //then
        $response->dump();
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonStructure([
                'success',
                'response'=>[
                    0 => ['id','title']
                ]
            ]);
    }

    /**
     * 사용자의 카테고리 모두 조회 성공 테스트 (category가 있을때)
     * @test
     * @return void
     */
    public function getUserCategoriesSuccessExistCategories()
    {
        //given
        $userId = $this->storeUserToSession();
        $title = "테스트 카테고리";
        $this->createCategory($title);

        //when
        $response = $this->getJson('/user/category');

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertSee('id')
            ->assertSee('title');
    }

    /**
     * 카테고리 조회 실패 테스트 / 없는 사용자 id
     * @test
     * @return void
     */
    public function getCategoryFailTest1()
    {
        //when
        $response = $this->getJson('/user/category');

        //then
        $response->assertStatus(401)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',401)
            ->assertJsonPath('response.message','인증되지 않은 사용자입니다.');
    }


    /**
     * 사용자의 카테고리 생성 성공 테스트
     * @test
     * @return void
     */
    public function createCategorySuccess()
    {
        //given
        $userId = $this->storeUserToSession();
        $title = "테스트 카테고리";

        //when
        $response = $this->postJson('/user/category',[
            'title' => $title
        ]);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.title',$title)
            ->assertSee('id');
    }

    /**
     * 사용자의 카테고리 생성 실패 테스트 | 이미 존재하는 카테고리
     * @test
     * @return void
     */
    public function createCategoryFailTestDuplicateTitle()
    {
        //given
        $userId =  $this->storeUserToSession();
        $title = "테스트 카테고리";
        $this->createCategory($title);

        //when
        $response = $this->postJson('/user/category',[
            'title' => $title
        ]);

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400)
            ->assertJsonPath('response.message',"이미 존재하는 카테고리입니다.");
    }

    /**
     * 사용자의 카테고리 수정 성공 테스트
     * @test
     * @return void
     */
    public function updateCategorySuccessTest()
    {
        //given
        $userId = $this->storeUserToSession();
        $title = "테스트 카테고리";
        $categoryId = $this->createCategory($title);
        $updateTitle = "수정한 카테고리";

        //when
        $response = $this->patchJson('/user/category/' . $categoryId,
            ['title' => $updateTitle]);

        //then
        $response->dump();
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.title',$updateTitle )
            ->assertJsonStructure([
                'success',
                'response' => [ 'id', 'title']
            ]);
    }

    /**
     * 사용자의 카테고리 수정 실패 테스트 | 없는 카테고리
     * @test
     * @return void
     */
    public function updateCategorySuccessFailNotExist()
    {
        //given
        $userId =  $this->storeUserToSession();
        $title = "테스트 카테고리";
        $categoryId = 139472673189;
        $updateTitle = "수정한 카테고리";

        //when
        $response = $this->patchJson('/user/category/' . $categoryId,
            ['title' => $updateTitle]);

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400 )
            ->assertJsonPath('response.message','카테고리가 존재하지 않습니다.');
    }

    /**
     * 사용자의 카테고리 수정 실패 테스트 | 요청 body가 존재x
     * @test
     * @return void
     */
    public function updateCategorySuccessFailBadRequest()
    {
        //given
        $userId =  $this->storeUserToSession();
        $title = "테스트 카테고리";
        $categoryId = 139472673189;

        //when
        $response = $this->patchJson('/user/category/' . $categoryId);

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400 )
            ->assertJsonPath('response.message','잘못된 요청입니다.');
    }

    /**
     * 사용자의 카테고리 삭제 성공 테스트
     * @test
     * @return void
     */
    public function deleteCategorySuccessTest()
    {
        //given
        $userId =  $this->storeUserToSession();
        $title = "테스트 카테고리";
        $categoryId = $this->createCategory($title);

        //when
        $response = $this->deleteJson('/user/category/' . $categoryId);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response',true )
            ->assertJsonStructure([
                'success',
                'response'
            ]);
    }

    /**
     * 사용자의 카테고리 삭제 삭제 테스트 | query Parameter 존재 x
     * @test
     * @return void
     */
    public function deleteCategoryFailTestNotExistQueryParameter()
    {
        //given
        $userId = $this->storeUserToSession();
        $title = "테스트 카테고리";
        $categoryId = $this->createCategory($title);

        //when
        $response = $this->deleteJson('/user/category');

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400 )
            ->assertJsonPath('response.message','잘못된 요청입니다.');
    }

    /**
     * 사용자의 카테고리 삭제 삭제 테스트 | 카테고리 존재 x
     * @test
     * @return void
     */
    public function deleteCategoryFailTestNotExistCategory()
    {
        //given
        $userId =  $this->storeUserToSession();
        $categoryId =1234232132412;

        //when
        $response = $this->deleteJson('/user/category/' . $categoryId);

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400 )
            ->assertJsonPath('response.message','카테고리가 존재하지 않습니다.');
    }
}
