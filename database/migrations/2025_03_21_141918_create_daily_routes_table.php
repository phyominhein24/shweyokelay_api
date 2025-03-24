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
        Schema::create('daily_routes', function (Blueprint $table) {
            $table->id();
            $table->string('driver_name')->nullable();
            $table->string('car_no')->nullable();
            $table->unsignedBigInteger('route_id');
            $table->string('status')->default(GeneralStatusEnum::ACTIVE->value);
            $table->auditColumns();

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_routes');
    }
};
