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
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Way\Way::class);
            $table->dropColumn('way_id');
            $table->unsignedBigInteger('part_way_id')->nullable();
            $table->foreign('part_way_id')->references('id')->on('part_ways');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Way\PartWay::class);
            $table->dropColumn('part_way_id');
            $table->unsignedBigInteger('way_id')->nullable();
            $table->foreign('way_id')->references('id')->on('ways');
        });
    }
};
