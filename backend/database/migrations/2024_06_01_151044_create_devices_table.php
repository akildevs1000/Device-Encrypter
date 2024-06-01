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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('model_number');
            $table->string('device_id');
            $table->string('name')->default('Default Name');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->unsignedBigInteger('company_id')->default(0);
            $table->string('ip')->default('0.0.0.0');
            $table->string('port')->default('0000');
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
        Schema::dropIfExists('devices');
    }
};
