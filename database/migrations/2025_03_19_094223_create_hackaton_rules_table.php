<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hackathon_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->nullable()->constrained('rules')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('hackathon_id')->nullable()->constrained('hackathons')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hackaton_rules');
    }
};
