<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Model;

class ConsultaPaciente extends Model
{
    protected $table = 'consultas';
	protected $guarded = array();

	public function paciente(){
		return $this->belongsTo(
			'App\Models\Odontologia\Paciente',
			'id_paciente'
		);
	}

	public function tratamiento(){
		return $this->belongsTo(
			'App\Models\Odontologia\Tratamiento',
			'id_tratamiento'
		);
	}
}
