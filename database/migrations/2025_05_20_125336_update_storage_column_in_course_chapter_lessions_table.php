<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_chapter_lessions', function (Blueprint $table) {
            DB::statement("ALTER TABLE course_chapter_lessions MODIFY COLUMN storage ENUM('upload', 'youtube', 'vimeo', 'external_link', 'html5') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_chapter_lessions', function (Blueprint $table) {
            DB::statement("ALTER TABLE course_chapter_lessions MODIFY COLUMN storage ENUM('upload', 'youtube', 'vimeo', 'external_link') NOT NULL");
        });
    }
};
