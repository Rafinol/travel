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
        Schema::table('ways_search', function (Blueprint $table) {
            $table->dropColumn('way_id');
            $table->foreignIdFor(\App\Models\Way\PartWay::class);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ways_search', function (Blueprint $table) {
            $table->dropColumn('part_way_id');
            $table->foreignIdFor(\App\Models\Way\Way::class);
        });
    }
};
