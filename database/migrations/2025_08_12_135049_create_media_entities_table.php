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
        Schema::create('media_entities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->string('business_name', 255);
            $table->enum('ownership_type', ['sole_proprietorship', 'partnership', 'corporation']);
            $table->enum('work_type', ['tv', 'radio', 'online_news', 'newspaper', 'other']);
            $table->string('other_work_type');
            $table->enum('reason', ['new_license', 'renewal']);
            $table->text('office_location');
            $table->string('phone', 50);
            $table->string('email', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_entities');
    }
};
