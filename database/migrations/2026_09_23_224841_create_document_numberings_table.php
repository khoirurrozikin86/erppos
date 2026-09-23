<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_numberings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->unique()
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->string('document_type', 50);
            $table->string('prefix', 20);
            $table->string('format', 100)->default('{PREFIX}/{YYYY}/{MM}/{NUMBER}');
            $table->unsignedInteger('next_number')->default(1);
            $table->unsignedTinyInteger('number_length')->default(5);

            $table->enum('reset_period', [
                'never',
                'yearly',
                'monthly',
            ])->default('monthly');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['company_id', 'document_type'],
                'document_numberings_company_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_numberings');
    }
};
