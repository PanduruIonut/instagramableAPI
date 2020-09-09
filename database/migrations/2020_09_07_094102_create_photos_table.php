<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->text('created_time');
            $table->text('lat');
            $table->text('long');
            $table->text('filter');
            $table->text('link');
            $table->text('low_resolution_url');
            $table->text('low_resolution_width');
            $table->text('low_resolution_height');
            $table->text('thumbnail_url');
            $table->text('thumbnail_width');
            $table->text('thumbnail_height');
            $table->text('standard_resolution_url');
            $table->text('standard_resolution_width');
            $table->text('standard_resolution_height');
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
        Schema::dropIfExists('photos');
    }
}
