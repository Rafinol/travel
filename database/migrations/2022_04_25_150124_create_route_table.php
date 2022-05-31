<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type')->comment('Moving, waiting');
            $table->timestamp('sdate');
            $table->timestamp('edate');
            $table->integer('price')->nullable();
            $table->integer('duration')->comment('seconds');
            $table->integer('from_id')->nullable();
            $table->integer('to_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route');
    }
};
