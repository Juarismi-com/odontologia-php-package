<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdontCaradiente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('odont_diente_cara', function (Blueprint $table) {
            $table->id();

            // Misma info que la del cliente
            $table->integer('paciente_id')->nullable();
            
            $table->integer('diente_nro');
            $table->string('diente_cara');
            $table->string('diente_estado');

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
        Schema::dropIfExists('odont_diente_cara');
    }
}
