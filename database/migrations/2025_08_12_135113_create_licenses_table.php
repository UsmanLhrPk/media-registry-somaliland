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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_entity_id')->constrained('media_entities')->onDelete('cascade');
            $table->string('license_number', 100)->unique();
            $table->enum('license_type', ['new', 'renewal'])->default('new');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->date('renewed_date')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};