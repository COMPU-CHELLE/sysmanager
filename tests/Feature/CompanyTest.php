<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_belong_to_multiple_companies(): void
    {
        $user = User::factory()->create();
        $companies = Company::factory()->count(2)->create();

        $user->companies()->attach($companies);

        $this->assertCount(2, $user->companies);
        $this->assertTrue($companies->first()->users->contains($user));
    }
}
