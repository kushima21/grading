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
        if (!Schema::hasTable('notif_table')) {
            Schema::create('notif_table', function (Blueprint $table) {
                $table->id('notif_id');
                $table->string('notif_type');
                $table->integer('class_id');
                $table->string('class_subject_code');
                $table->string('class_descriptive_title');
                $table->string('department');
                $table->integer('added_by_id');
                $table->string('added_by_name');
                $table->integer('target_by_id');
                $table->string('target_by_name');
                $table->string('status_from_added');
                $table->string('status_from_target');
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
