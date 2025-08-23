<?php

use App\Models\FailureReport;
use App\Models\Person;
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
        Schema::create('failure_report_person', function (Blueprint $table) {
            // sin $table->id(); en una pivot estándar

            $table->foreignIdFor(FailureReport::class)
                ->constrained()         // ->references('id')->on('failure_reports')
                ->cascadeOnDelete();

            $table->foreignIdFor(Person::class)
                ->constrained()         // ->references('id')->on('people')
                ->cascadeOnDelete();

            // Clave primaria compuesta (recomendado)
            $table->primary(['failure_report_id', 'person_id']);

            // Si deseas timestamps en el pivot, descomenta:
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failure_report_person');
    }
};
