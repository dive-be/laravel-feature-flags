<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturesTable extends Migration
{
    public function up()
    {
        Schema::create('features', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scope');
            $table->string('label');
            $table->string('description');
            $table->string('message')->nullable();
            $table->timestamp('disabled_at')->nullable();

            $table->unique(['scope', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('features');
    }
}
