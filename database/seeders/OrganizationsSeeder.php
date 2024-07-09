<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Repository;
use Illuminate\Database\Seeder;

class OrganizationsSeeder extends Seeder
{
    public function run(): void
    {
        Organization::factory()
            ->count(10)
            ->create()
            ->each(function ($organization): void {
                Repository::factory()->count(5)->create([
                    "organization_id" => $organization->id,
                ]);
            });
    }
}
