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
        if (!Schema::hasTable('final_transmutation')) {
            Schema::create('final_transmutation', function (Blueprint $table) {
                $table->id();

                $table->decimal('grades', 5, 2);

                $table->decimal('transmutation', 5,2);

                $table->string('remarks');

                $table->timestamps();
            });

            DB::table('final_transmutation')->insert([
                ['grades' => '1.00',  'transmutation' => '1.00', 'remarks' => 'Excellent', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '1.24',  'transmutation' => '1.00', 'remarks' => 'Excellent', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '1.49',  'transmutation' => '1.25', 'remarks' => 'Very Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '1.25',  'transmutation' => '1.25', 'remarks' => 'Very Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '1.50',  'transmutation' => '1.50', 'remarks' => 'Very Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '1.74',  'transmutation' => '1.50', 'remarks' => 'Very Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '1.75',  'transmutation' => '1.75', 'remarks' => 'Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '1.99',  'transmutation' => '1.75', 'remarks' => 'Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.00',  'transmutation' => '2.00', 'remarks' => 'Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.24',  'transmutation' => '2.00', 'remarks' => 'Satisfactory', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.25',  'transmutation' => '2.25', 'remarks' => 'Fair', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.49',  'transmutation' => '2.25', 'remarks' => 'Fair', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.50',  'transmutation' => '2.50', 'remarks' => 'Fair', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.74',  'transmutation' => '2.55', 'remarks' => 'Fair', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.75',  'transmutation' => '2.75', 'remarks' => 'Poor', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '2.99',  'transmutation' => '2.75', 'remarks' => 'Poor', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '3.00',  'transmutation' => '3.00', 'remarks' => 'Poor', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '3.24',  'transmutation' => '3.00', 'remarks' => 'Poor', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '3.30',  'transmutation' => '5.00', 'remarks' => 'Failed', 'created_at' => now(), 'updated_at' => now()],
                ['grades' => '5.00',  'transmutation' => '5.00', 'remarks' => 'Failed', 'created_at' => now(), 'updated_at' => now()],


            ]);
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
