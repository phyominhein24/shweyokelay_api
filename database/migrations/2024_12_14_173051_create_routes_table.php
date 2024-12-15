<?php

use App\Enums\GeneralStatusEnum;
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
        Schema::create('routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->unsignedBigInteger('vehicles_type_id');
            $table->unsignedBigInteger('starting_point');
            $table->unsignedBigInteger('ending_point');
            $table->string('distance');
            $table->string('duration');
            $table->boolean('is_ac');
            $table->json('day_off')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->string('price');
            $table->string('departure');
            $table->string('arrivals');
            $table->string('status')->default(GeneralStatusEnum::ACTIVE->value);
            $table->auditColumns();

            $table->foreign('starting_point')
                ->references('id')
                ->on('counters')
                ->onDelete('cascade');

            $table->foreign('ending_point')
                ->references('id')
                ->on('counters')
                ->onDelete('cascade');

            $table->foreign('vehicles_type_id')
                ->references('id')
                ->on('vehicles_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
