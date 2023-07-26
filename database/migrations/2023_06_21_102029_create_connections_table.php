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
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requestor_id');
            $table->unsignedBigInteger('requestee_id');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->foreign('requestor_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('requestee_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('connections');
    }
};
