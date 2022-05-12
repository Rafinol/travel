<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Way\Way;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_ways', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Way::class);
            $table->integer('position');
            $table->timestamp('departure_date')->nullable();
            $table->timestamp('arrival_date')->nullable();
            $table->unsignedBigInteger('from_id');
            $table->unsignedBigInteger('to_id');
            $table->foreign('from_id')->references('id')->on('cities');
            $table->foreign('to_id')->references('id')->on('cities');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_ways');
    }
};
