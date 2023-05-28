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
        Schema::create('faculty_positions', function (Blueprint $table) {
            $table->id();
            $table->string('position_name');
            $table->integer('target_per_function');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('faculty_position_id')->references('id')->on('faculty_positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faculty_positions');
    }
};
