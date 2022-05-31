<?php

use App\Models\Way\PartWay;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Route\RouteSearchForm;

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
            $table->dropColumn('part_way_id');
            $table->foreignIdFor(RouteSearchForm::class);
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
            //$table->dropForeignIdFor(RouteSearchForm::class);
            $table->dropColumn('route_search_form_id');
            $table->addColumn('integer','part_way_id');
        });
    }
};
