<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('title'); // judul pengumuman
            $table->longText('content'); // isi pengumuman
            $table->string('image_path')->nullable(); // path atau URL gambar, bisa null
            $table->dateTime('publish_at')->nullable(); // tanggal publikasi
            $table->boolean('is_active')->default(true); // status aktif/tidak
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
