<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan locale Indonesia agar teksnya bahasa Indonesia (opsional)
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 10; $i++) {
            
            // Simulasi konten dari Text Editor (Rich Text)
            // Kita gabungkan tag HTML agar terlihat realistik
            $htmlContent = '<h3>' . $faker->sentence() . '</h3>';
            $htmlContent .= '<p>' . $faker->paragraph(3) . '</p>';
            $htmlContent .= '<p><strong>Poin-poin penting:</strong></p>';
            $htmlContent .= '<ul>';
            for ($j = 0; $j < 3; $j++) {
                $htmlContent .= '<li>' . $faker->sentence() . '</li>';
            }
            $htmlContent .= '</ul>';
            $htmlContent .= '<p><em>Ditulis pada tanggal: ' . date('d-m-Y') . '</em></p>';

            DB::table('announcements')->insert([
                'title'      => $faker->sentence(6), // Judul acak
                'content'    => $htmlContent,        // Konten HTML simulasi text editor
                'image_path' => null, 
                'publish_at' => $faker->dateTimeBetween('-1 month', '+1 week'), // Tanggal random (bulan lalu s/d minggu depan)
                'is_active'  => $faker->boolean(90), // 90% kemungkinan aktif
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}