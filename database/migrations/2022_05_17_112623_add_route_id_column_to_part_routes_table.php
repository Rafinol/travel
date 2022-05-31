<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Route\Route;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('part_routes', function (Blueprint $table) {
            $table->dropColumn('index');
            $table->foreignIdFor(Route::class);
            $table->dropColumn('route_search_form_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('part_routes', function (Blueprint $table) {
            $table->integer('index');
            $table->dropColumn('route_id');
            $table->foreignIdFor(\App\Models\Route\RouteSearchForm::class);
        });
    }
};
