<?php

/**
 * No utilizado de momento
 */
namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    protected $table = 'odont_fichapaciente';
	protected $guarded = array();


	public function paciente(){
		return $this->belongsTo(
			'App\Models\Odontologia\Paciente',
			'id_paciente'
		);
	}

	public function paciente(){
		return $this->belongsTo(
			'App\Models\Odontologia\Paciente',
			'id_paciente'
		);
	}



	public function dientes(){
		// return $this->belongsTo(
		// 	'App\Models\Common\Diente',
		// 	'id_paciente'
		// );
	}


}
