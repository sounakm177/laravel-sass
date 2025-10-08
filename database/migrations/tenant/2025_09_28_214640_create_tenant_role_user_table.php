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
        Schema::create('tenant_role_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_user_id');
            $table->unsignedBigInteger('tenant_role_id');
            $table->timestamps();

            $table->foreign('tenant_user_id')->references('id')->on('tenant_users')->onDelete('cascade');
            $table->foreign('tenant_role_id')->references('id')->on('tenant_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_role_user');
    }
};
