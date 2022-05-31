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
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('from');
            $table->dropColumn('to');
            $table->unsignedBigInteger('from_id')->nullable();
            $table->unsignedBigInteger('to_id')->nullable();
            $table->foreign('from_id')->references('id')->on('cities');
            $table->foreign('to_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('from');
            $table->string('to');
            $table->dropForeign(['from_id']);
            $table->dropForeign(['to_id']);
            $table->dropColumn('from_id');
            $table->dropColumn('to_id');
        });
    }
};
