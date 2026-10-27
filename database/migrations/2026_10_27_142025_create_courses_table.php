<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('kind'); // path | topic
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('icon');
            $table->unsignedTinyInteger('grade_min')->default(1);
            $table->unsignedTinyInteger('grade_max')->default(12);
            $table->boolean('is_premium')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
