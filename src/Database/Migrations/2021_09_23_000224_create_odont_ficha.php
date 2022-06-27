<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdontFicha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('odont_ficha', function (Blueprint $table) {
            $table->id();
            $table->integer('cliente_id')->nullable();
            $table->integer('paciente_id')->nullable();
            $table->dateTime('fecha_ini')->nullable();

            $table->string('motivo', 255)->nulalble();
            $table->string('cara', 50)->nullable();
            $table->string('labios_comisura', 50)->nullable();
            $table->string('ganglios', 50)->nullable();
            $table->string('atm', 50)->nullable();

            // Examenes
            $table->string('region_hioidea_tiroidea', 255)->nullable();
            $table->string('ex_carrillos', 255)->nullable();
            $table->string('ex_mucosa', 255)->nullable();
            $table->string('ex_encia', 255)->nullable();
            $table->string('ex_lengua', 255)->nullable();
            $table->string('ex_paladar', 255)->nullable();
            $table->string('ex_clinico_otros', 255)->nullable();
            $table->string('ex_comple_otros', 255)->nullable();
            $table->string('ex_comple_rx', 255)->nullable();
            $table->string('ex_comple_ortopantomografia', 255)->nullable();
            $table->string('ex_comple_lateral_craneo', 255)->nullable();
            $table->string('ex_comple_frontal', 255)->nullable();
            $table->string('ex_comple_periapical', 255)->nullable();

            // Antecedente
            $table->integer('ant_tratamiento_actual')->nullable();
            $table->integer('ant_consume_medicamento')->nullable();            
            $table->integer('ant_cx')->nullable();            
            $table->integer('ant_transfucion_sanguinea')->nullable();
            $table->integer('ant_consume_droga')->nullable();            
            $table->integer('ant_alergico_penicilina')->nullable();
            $table->integer('ant_alergico_anestecia')->nullable();
            $table->integer('ant_alergico_aspirina')->nullable();
            $table->integer('ant_presion_arterial')->nullable();     
            $table->integer('ant_sangra_mucho')->nullable();
            $table->integer('ant_problema_sanguineo')->nullable();
            $table->integer('ant_posee_vih')->nullable();
            $table->integer('ant_toma_retroviral')->nullable();
            $table->integer('ant_esta_embarazada')->nullable();
            $table->integer('ant_consume_anticonceptivo')->nullable();
            $table->integer('ant_enfermedad_venerea')->nullable();
            $table->integer('ant_problema_cardiado')->nullable();
            $table->integer('ant_hepatitis')->nullable();
            $table->integer('ant_fibre_reumatica')->nullable();
            $table->integer('ant_asma')->nullable();
            $table->integer('ant_diabete')->nullable();
            $table->integer('ant_ulcera_gastrica')->nullable();
            $table->integer('ant_tiroides')->nullable();
            $table->integer('ant_le_cuesta_boca')->nullable();
            $table->integer('ant_ruidos_mandibula')->nullable();
            $table->integer('ant_herpes')->nullable();
            $table->integer('ant_muerde_unha')->nullable();
            $table->integer('ant_fuma')->nullable();
            $table->integer('ant_cantidad_cigarillos')->nullable();
            $table->integer('ant_consume_citricos')->nullable();
            $table->integer('ant_muerde_objetos')->nullable();
            $table->integer('ant_bruxomano')->nullable();

            $table->text('observaciones')->nullable();
            $table->text('dianostico_plan')->nullable();
            $table->integer('estado')->default(1);
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
        Schema::dropIfExists('odont_ficha');
    }
}
