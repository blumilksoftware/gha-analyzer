<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\MemberDTO;
use App\DTO\OrganizationDTO;
use App\Models\Organization;
use App\Models\User;
use App\Services\GithubWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveMemberTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;
    private MemberDTO $memberDto;
    private OrganizationDTO $organizationDto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(["github_id" => 123]);
        $this->organization = Organization::factory()->create(["github_id" => 456]);
        $this->user->organizations()->attach($this->organization->id);

        $this->memberDto = new MemberDTO(123);
        $this->organizationDto = new OrganizationDTO("test", 456, "http://example.com/avatar.png");
    }

    public function testRemoveMember(): void
    {
        $this->assertDatabaseHas("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);

        $service = new GithubWebhookService();
        $service->removeMember($this->organizationDto, $this->memberDto);

        $this->assertDatabaseMissing("user_organization", [
            "organization_id" => $this->organization->id,
            "user_id" => $this->user->id,
        ]);
    }
}
