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
        if (!Schema::hasTable('quizzes_scores')) {
            Schema::create('quizzes_scores', function (Blueprint $table) {
                $table->id();
                $table->integer('classID');
                $table->integer('studentID');
                $table->string('periodic_term');
                $table->decimal('quizzez', 5, 2)->nullable();
                $table->decimal('attendance_behavior', 5, 2)->nullable();
                $table->decimal('assignments', 5, 2)->nullable();
                $table->decimal('exam', 5, 2)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
