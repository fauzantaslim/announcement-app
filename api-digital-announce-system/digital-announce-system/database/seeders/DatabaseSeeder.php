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
        // 1. Panggil seeder untuk data Pengumuman (Announcement)
        $this->call(AnnouncementSeeder::class); // <-- Tambahkan baris ini
        // Create specific test users with known passwords
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'), // Password: password123
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'), // Password: password123
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'), // Password: password123
        ]);

        // Optionally, create additional random users using factory
        // User::factory(10)->create();
    }
}
