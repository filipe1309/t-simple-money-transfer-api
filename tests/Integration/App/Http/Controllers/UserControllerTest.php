<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_can_list_all_users(): void
    {
        $contentJson = $this->get("v1/users")->response->getContent();
        $content = json_decode($contentJson);

        $this->seeStatusCode(206);
        $this->seeJsonStructure(['data' => 'data']);
        $this->assertCount(4, $content->data->data);
    }

    public function test_can_list_an_specific_user(): void
    {
        $user = User::factory()->create();

        $this->get("v1/users/{$user->id}");

        $this->seeStatusCode(200);
        $this->seeJsonStructure(['data' => ['id', 'email']]);
        $this->seeInDatabase('users', [
            'id' => $user->id,
            'email' => $user->email
        ]);
    }
}
