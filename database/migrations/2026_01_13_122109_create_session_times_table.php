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
        Schema::create('session_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained("live_sessions")->cascadeOnUpdate()->cascadeOnDelete();
            $table->time("from");
            $table->time("to");
            $table->enum("day", ['SUNDAY', "MONDAY", "TUESDAY",
            "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY"]);
            $table->timestamps();
        });
    } 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_times');
    }
};
