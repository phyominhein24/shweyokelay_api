<?php

use App\Enums\OrderStatusEnum;
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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('prepay_id')->unique();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->string('kpay_member_id')->nullable();
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('daily_route_id')->nullable();
            $table->string('screenshot')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('nrc')->nullable();;
            $table->json('seat');
            $table->integer('total');
            $table->string('note');
            $table->timestamp('start_time')->nullable();
            $table->string('status')->default(OrderStatusEnum::PENDING->value);
            $table->auditColumns();

            $table->foreign('member_id')
                ->references('id')
                ->on('members')
                ->onDelete('cascade');

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');

            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('cascade');
            
            $table->foreign('daily_route_id')
                ->references('id')
                ->on('daily_routes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
