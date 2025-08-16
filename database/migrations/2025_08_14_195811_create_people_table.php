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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cargo')->nullable();
            $table->string('empresa')->nullable(); // Ej. PETROPERÚ, SERVOSA, etc.
            $table->foreignId('location_id')->constrained()->cascadeOnDelete(); // sede            
            $table->softDeletes();
            $table->timestamps();
        });
    

        Schema::create('failure_report_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('failure_report_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('people_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unique(['failure_report_id', 'people_id']);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
        Schema::dropIfExists('failure_report_people');
    }
};
