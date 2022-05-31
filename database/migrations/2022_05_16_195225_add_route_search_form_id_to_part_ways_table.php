<?php

use App\Models\Route\RouteSearchForm;
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
        Schema::table('part_ways', function (Blueprint $table) {
            $table->foreignIdFor(RouteSearchForm::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('part_ways', function (Blueprint $table) {
            $table->dropColumn('route_search_form_id');
        });
    }
};
