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
            $table->rename('routes_search');
        });
        Schema::table('route_search', function (Blueprint $table) {
            $table->rename('route_search_form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes_search', function (Blueprint $table) {
            $table->rename('ways_search');
        });
        Schema::table('route_search_form', function (Blueprint $table) {
            $table->rename('route_search');
        });
    }
};
