<?php


namespace Tests\Feature\ControllerTests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseTransactions,WithoutMiddleware,ControllerTestUtil;

    /**
     * 사용자의 카테고리 모두 조회 성공 테스트 (category가 없을때)
     * @test
     * @return void
     */
    public function getUserCategoriesSuccessNoCategory()
    {
        //given
        $userId = 12345678;

        //when
        $response = $this->getJson('/user/' . $userId . '/category');

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response','null');
    }

    /**
     * 사용자의 카테고리 모두 조회 성공 테스트 (category가 있을때)
     * @test
     * @return void
     */
    public function getUserCategoriesSuccessExistCategories()
    {
        //given
        $userId = 1;
        $title = "테스트 카테고리";
        $this->createCategory($userId,$title);

        //when
        $response = $this->getJson('/user/' . $userId . '/category');

        //then
        $response->dump();
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
    public function getUserInfoFailTest1()
    {
        //given
        $userId = 482819 . rand(0,10000);

        //when
        $response = $this->getJson('/user/' . $userId);

        //then
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 사용자입니다.');
    }


    /**
     * 사용자의 카테고리 생성 성공 테스트
     * @test
     * @return void
     */
    public function createCategorySuccess()
    {
        //given
        $userId = 1;
        $title = "테스트 카테고리";

        //when
        $response = $this->postJson('/user/' . $userId . '/category',[
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
        $userId = 1;
        $title = "테스트 카테고리";
        $this->createCategory($userId,$title);

        //when
        $response = $this->postJson('/user/' . $userId . '/category',[
            'title' => $title
        ]);

        //then
        $response->assertStatus(400)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',400)
            ->assertJsonPath('response.message',"이미 존재하는 카테고리입니다.");
    }
}
