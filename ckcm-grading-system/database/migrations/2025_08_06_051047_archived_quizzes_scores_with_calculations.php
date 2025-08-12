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
        if (!Schema::hasTable('final_grade')) {
            Schema::create('final_grade', function (Blueprint $table) {
                $table->id();
                $table->integer('classID');
                $table->string('course_no')->nullable();
                $table->string('descriptive_title')->nullable();
                $table->string('instructor')->nullable();
                $table->string('academic_period')->nullable();
                $table->string('schedule')->nullable();
                $table->integer('studentID');
                $table->string('name')->nullable();
                $table->string('gender')->nullable();
                $table->string('email')->nullable();
                $table->string('department')->nullable();
                $table->decimal('prelim', 5,2);
                $table->decimal('midterm', 5,2);
                $table->decimal('semi_finals', 5,2);
                $table->decimal('final', 5,2);
                $table->string('remarks')->nullable();
                $table->text('status')->nullable();
                $table->text('submit_status')->nullable();
                $table->text('dean_status')->nullable();
                $table->text('registrar_status')->nullable();
                $table->text('dean_comment')->nullable();
                $table->text('registrar_comment')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
