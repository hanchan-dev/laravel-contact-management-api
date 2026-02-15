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
        Schema::create('user_session', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->foreignId("token_id")->constrained("personal_access_tokens")->cascadeOnDelete();
            $table->string("platform")->nullable();
            $table->string("browser")->nullable();
            $table->string("device_type")->nullable();
            $table->ipAddress("ip_address")->nullable();
            $table->timestamp("logged_in_at");
            $table->timestamp("logged_out_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_session');
    }
};
