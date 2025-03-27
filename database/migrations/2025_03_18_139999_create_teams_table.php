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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('project')->nullable();
            $table->foreignId('hackathon_id')->nullable()->constrained('hackathons')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('jurie_id')->nullable()->constrained('juries')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('theme_id')->nullable()->constrained('themes')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('teams');
    }
};
