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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->integer("studentID")->nullable();
                $table->string('name');
                $table->string('gender')->nullable();
                $table->string('email')->unique();
                $table->string('department')->nullable();
                $table->string('password');
                $table->string("role")->nullable();
                $table->timestamps();
            });


            DB::table('users')->insert([
                ['studentID' => '000001', 'name' => 'Student Computer Science', 'gender' => 'male', 'email' => 'studentcomscie@ckcm.edu.ph', 'department' => 'Bachelor of Science in Computer Science', 'password' => bcrypt('12345'), 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000002', 'name' => 'Student Business Administration', 'gender' => 'male', 'email' => 'studentbusiness@ckcm.edu.ph', 'department' => 'Bachelor of Science in Business Administration', 'password' => bcrypt('12345'), 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000003', 'name' => 'Student Criminology', 'gender' => 'male', 'email' => 'studentcrim@ckcm.edu.ph', 'department' => 'Bachelor of Science in Criminology', 'password' => bcrypt('12345'), 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000018', 'name' => 'Student Baels', 'gender' => 'male', 'email' => 'studentbaels@ckcm.edu.ph', 'department' => 'Bachelor of Arts in English Language Studies', 'password' => bcrypt('12345'), 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000019', 'name' => 'Student Social Work', 'gender' => 'male', 'email' => 'studentsocialwork@ckcm.edu.ph', 'department' => 'Bachelor of Science in Social Work', 'password' => bcrypt('12345'), 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000020', 'name' => 'Student Education', 'gender' => 'male', 'email' => 'studenteducation@ckcm.edu.ph', 'department' => 'Bachelor of Science in Education', 'password' => bcrypt('12345'), 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],

                ['studentID' => '000004', 'name' => 'Instuctor Computer Science', 'gender' => 'male', 'email' => 'instructorcomscie@ckcm.edu.ph', 'department' => 'Bachelor of Science in Computer Science', 'password' => bcrypt('12345'), 'role' => 'instructor', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000005', 'name' => 'Instuctor Business Administration', 'gender' => 'male', 'email' => 'instructorcba@ckcm.edu.ph', 'department' => 'Bachelor of Science in Business Administration', 'password' => bcrypt('12345'), 'role' => 'instructor', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000006', 'name' => 'Instuctor Criminology', 'gender' => 'male', 'email' => 'instructorcrim@ckcm.edu.ph', 'department' => 'Bachelor of Science in Criminology', 'password' => bcrypt('12345'), 'role' => 'instructor', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000007', 'name' => 'Instuctor Baels', 'gender' => 'male', 'email' => 'instructorbaels@ckcm.edu.ph', 'department' => 'Bachelor of Arts in English Language Studies', 'password' => bcrypt('12345'), 'role' => 'instructor', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000008', 'name' => 'Instuctor Social Work', 'gender' => 'male', 'email' => 'instructorsocialwork@ckcm.edu.ph', 'department' => 'Bachelor of Science in Social Work', 'password' => bcrypt('12345'), 'role' => 'instructor', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000009', 'name' => 'Instuctor Education', 'gender' => 'male', 'email' => 'instructoreducation@ckcm.edu.ph', 'department' => 'Bachelor of Science in Education', 'password' => bcrypt('12345'), 'role' => 'instructor', 'created_at' => now(), 'updated_at' => now()],

                ['studentID' => '000010', 'name' => 'Dean Computer Science', 'gender' => 'male', 'email' => 'deancomscie@ckcm.edu.ph', 'department' => 'Bachelor of Science in Computer Science', 'password' => bcrypt('12345'), 'role' => 'instructor,dean', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000011', 'name' => 'Dean Business Administration', 'gender' => 'male', 'email' => 'deancba@ckcm.edu.ph', 'department' => 'Bachelor of Science in Business Administration', 'password' => bcrypt('12345'), 'role' => 'instructor,dean', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000012', 'name' => 'Dean Criminology', 'gender' => 'male', 'email' => 'deancrim@ckcm.edu.ph', 'department' => 'Bachelor of Science in Criminology', 'password' => bcrypt('12345'), 'role' => 'instructor,dean', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000013', 'name' => 'Dean Baels', 'gender' => 'male', 'email' => 'deanbaels@ckcm.edu.ph', 'department' => 'Bachelor of Arts in English Language Studies', 'password' => bcrypt('12345'), 'role' => 'instructor,dean', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000014', 'name' => 'Dean Social Work', 'gender' => 'male', 'email' => 'deansocialwork@ckcm.edu.ph', 'department' => 'Bachelor of Science in Social Work', 'password' => bcrypt('12345'), 'role' => 'instructor,dean', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000015', 'name' => 'Dean Education', 'gender' => 'male', 'email' => 'deaneducation@ckcm.edu.ph', 'department' => 'Bachelor of Science in Education', 'password' => bcrypt('12345'), 'role' => 'instructor,dean', 'created_at' => now(), 'updated_at' => now()],

                ['studentID' => '000016', 'name' => 'Registrar', 'gender' => 'female', 'email' => 'registrar@ckcm.edu.ph', 'department' => 'N/A', 'password' => bcrypt('12345'), 'role' => 'registrar', 'created_at' => now(), 'updated_at' => now()],
                ['studentID' => '000017', 'name' => 'Admin', 'gender' => 'admin', 'email' => 'admin@ckcm.edu.ph', 'department' => 'Admin Department', 'password' => bcrypt('12345'), 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],


            ]);
        }


        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
