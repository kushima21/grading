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
        if (!Schema::hasTable('classes_student')) {
            Schema::create('classes_student', function (Blueprint $table) {
                $table->id();
                $table->integer('classID');
                $table->integer('studentID');
                $table->string('name');
                $table->string('gender')->nullable();
                $table->string('email');
                $table->string('department');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes_student');
    }
};
