<?php

namespace Tests\Feature\ControllerTests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions,WithoutMiddleware;
    /**
     * User 정보 get 성공 테스트
     * @test
     * @return void
     */
    public function getUserInfoSuccess()
    {
        //given
        $userId = 1;

        //when
        $response = $this->getJson('/user/' . $userId);

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.name','홍의성')
            ->assertSee('created_at')
            ->assertSee('updated_at');


    }

    /**
     * User 정보 get 실패 테스트 / 없는 id
     * @test
     * @return void
     */
    public function getUserInfoFailTest1()
    {
        //given
        $userId = 482819;

        //when
        $response = $this->getJson('/user/' . $userId);

        //then
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 사용자입니다.');
    }
}
