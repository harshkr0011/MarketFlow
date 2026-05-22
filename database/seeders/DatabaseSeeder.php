<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Spatie Roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Agency Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Staff']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Client']);

        // Create default Agency and Workspace
        $agency = \App\Models\Agency::create([
            'name' => 'Acme Marketing Agency',
        ]);

        $workspace = \App\Models\Workspace::create([
            'agency_id' => $agency->id,
            'name' => 'Summer Launch Workspace',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => true,
            'agency_id' => $agency->id,
        ])->assignRole('Super Admin');

        $agency->update(['owner_id' => $user->id]);

        $this->call([
            DashboardSeeder::class,
            AssetSeeder::class,
            EPortalSeeder::class,
            CoreAndAdvancedSeeder::class,
        ]);
    }
}
