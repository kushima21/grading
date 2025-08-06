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
        if (!Schema::hasTable('percentage')) {
            Schema::create('percentage', function (Blueprint $table) {
                $table->id();
                $table->integer('classID');
                $table->string('periodic_term');
                $table->integer('quiz_percentage')->nullable();
                $table->integer('quiz_total_score')->nullable();
                $table->integer('attendance_percentage')->nullable();
                $table->integer('attendance_total_score')->nullable();
                $table->integer('assignment_percentage')->nullable();
                $table->integer('assignment_total_score')->nullable();
                $table->integer('exam_percentage')->nullable();
                $table->integer('exam_total_score')->nullable();
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
