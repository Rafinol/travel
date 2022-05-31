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
        Schema::table('route', function (Blueprint $table) {
            $table->string('from_id', 50)->change();
            $table->string('to_id', 50)->change();
            $table->foreign('from_id')->references('code')->on('point');
            $table->foreign('to_id')->references('code')->on('point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route', function (Blueprint $table) {
            $table->dropForeign(['from_id']);
            $table->dropForeign(['to_id']);
        });
    }
};
