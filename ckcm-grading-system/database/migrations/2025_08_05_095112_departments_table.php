<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('department_name');
                $table->timestamps();
            });

            DB::table('departments')->insert([
                ['department_name' => 'Bachelor of Arts in English Language Studies', 'created_at' => now(), 'updated_at' => now()],
                ['department_name' => 'Bachelor of Science in Business Administration', 'created_at' => now(), 'updated_at' => now()],
                ['department_name' => 'Bachelor of Science in Computer Science', 'created_at' => now(), 'updated_at' => now()],
                ['department_name' => 'Bachelor of Science in Criminology', 'created_at' => now(), 'updated_at' => now()],
                ['department_name' => 'Bachelor of Science in Education', 'created_at' => now(), 'updated_at' => now()],
                ['department_name' => 'Bachelor of Science in Social Work', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
