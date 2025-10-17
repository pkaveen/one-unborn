<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserTypeSeeder::class,    // Run first
            MenuSeeder::class,        // Create menus before assigning privileges
            SuperAdminSeeder::class,  // Then create admin and assign privileges
            EmailTemplateSeeder::class, // Finally add default email templates
            
        ]);
    }
}
