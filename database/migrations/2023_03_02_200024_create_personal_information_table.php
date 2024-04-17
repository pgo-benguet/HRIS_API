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
        Schema::create('personal_information', function (Blueprint $table) {
            $table->id();

            // $table->foreignId('personal_data_sheet_id')->constrained('personal_data_sheets');
            $table->foreignId('personal_data_sheet_id')->constrained()->onDelete('cascade');

            $table->string('birthplace');
            $table->date('birthdate');
            $table->integer('age');
            $table->string('sex');
            $table->string('height');
            $table->string('weight');
            $table->string('citizenship');
            $table->enum('citizenship_type', ["By Birth", "By Naturalization"]);
            $table->string('country')->nullable();
            $table->string('blood_type');
            $table->enum('civil_status',['Single', 'Married', 'Divorced', 'Widowed']);
            $table->string('tin');
            $table->string('gsis');
            $table->string('pagibig');
            $table->string('philhealth');
            $table->string('sss');
            $table->string('residential_province');
            $table->string('residential_municipality');
            $table->string('residential_barangay');
            $table->string('residential_house');
            $table->string('residential_subdivision');
            $table->string('residential_street');
            $table->string('residential_zipcode');
            $table->string('permanent_province');
            $table->string('permanent_municipality');
            $table->string('permanent_barangay');
            $table->string('permanent_house');
            $table->string('permanent_subdivision');
            $table->string('permanent_street');
            $table->string('permanent_zipcode');
            $table->string('telephone');
            $table->string('mobile_number');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_information');
    }
};
