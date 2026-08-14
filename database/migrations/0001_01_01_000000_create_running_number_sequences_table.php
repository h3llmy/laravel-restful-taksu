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
        Schema::create('running_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g. "tenant_id:ModelClass"
            $table->unsignedBigInteger('next_value')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_number_sequences');
    }
};
