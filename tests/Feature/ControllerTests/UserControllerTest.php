<?php

namespace Tests\Feature\ControllerTests;

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * User 정보 get 성공 테스트
     * @test
     * @return void
     */
    public function getUserInfoSuccess()
    {
        $userId = 1;
        $response = $this->getJson('/user/' . $userId);
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertSee('id')
            ->assertJsonPath('response.name','홍의성')
            ->assertJsonPath('response.email','gowoonsori97@gmail.com')
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
        $userId = 482819;
        $response = $this->getJson('/user/' . $userId);
        $response->assertStatus(404)
            ->assertJsonPath('success',false)
            ->assertJsonPath('response.status',404)
            ->assertJsonPath('response.message','존재하지 않은 사용자입니다.');
    }
}
