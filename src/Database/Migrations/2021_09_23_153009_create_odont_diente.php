<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdontDiente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('odont_diente', function (Blueprint $table) {
            $table->id();

            // Misma info que la del cliente
            $table->integer('paciente_id')->nullable();
            
            $table->integer('diente_nro');
            $table->enum('tiene_diente', ["si", "no"])->default("no");
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
        Schema::dropIfExists('odont_diente');
    }
}
