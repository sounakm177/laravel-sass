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
        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 255);
            $table->string('last_name', 255)->nullable();
            $table->string('email', 255)->unique();
            $table->string('phone_no', 20)->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->timestamp('password_updated_at')->nullable();
            $table->string('one_time_token', 255)->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Optional foreign key to another tenant user
            $table->foreign('created_by')->references('id')->on('tenant_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_users');
    }
};
