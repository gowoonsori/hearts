<?php

namespace Tests\Feature\ControllerTests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions,WithoutMiddleware,ControllerTestUtil;

    /**
     * User 정보 get 성공 테스트
     * @test
     * @return void
     */
    public function getUserInfoSuccess()
    {
        //given
        $this->storeUserToSession();

        //when
        $response = $this->getJson('/user');

        //then
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response.name','홍의성');
    }

    /**
     * User 정보 get테스트 / 없는 id
     * @test
     * @return void
     */
    public function getUserInfoTest()
    {
        //when
        $response = $this->getJson('/user');

        //then
        $response->dump();
        $response->assertStatus(200)
            ->assertJsonPath('success',true)
            ->assertJsonPath('response',null);
    }
}
