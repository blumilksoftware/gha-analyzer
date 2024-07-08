<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Repository;

class OrganizationsSeeder extends Seeder
{
    public function run()
    {
        Organization::factory()
            ->count(10)
            ->create()
            ->each(function ($organization) {
                Repository::factory()->count(5)->create([
                    'organization_id' => $organization->id,
                ]);
            });
    }
}
