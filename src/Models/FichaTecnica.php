<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Model;

class FichaTecnica extends Model
{
    protected $table = 'odont_paciente';
	protected $guarded = array();


	/**
	 * Paciente relacionado a la ficha odontologica
	 */
	public function paciente(){
	 	return $this->belongsTo(
			'App\Models\Negocio\Cliente',
			'id_paciente',
			'id'
		);		
	}

}
