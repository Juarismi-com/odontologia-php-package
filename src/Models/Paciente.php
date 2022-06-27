<?php

namespace Juarismi\Odontologia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Model\Negocio\Cliente;

class Paciente extends Cliente
{


	/**
	 * Procedimientos / Tratamientos realizados al  paciente/cliente
	 */
	public function procedimientos(){
		return $this->hasMany(
			'App\Model\Medicina\Procedimiento',
			'id_paciente',
			'id'
		);		
	}

	/**
	 * Retorna la ficha del paciente
	 */
	public function ficha(){
		return $this->hasOne(
			'App\Model\Odontologia\FichaOdontologica',
			'id_paciente',
			'id'
		);			
	}


	/**
	 * Listado de dientes que no tiene el paciente
	 */
	public function dientes(){
		return $this->hasMany(
			'App\Model\Odontologia\Diente',
			'id_paciente',
			'id'
		);				
	}
}
