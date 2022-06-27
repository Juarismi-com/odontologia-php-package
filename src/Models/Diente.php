<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Model;

class Diente extends Model
{
    protected $table = 'odont_diente';
	protected $guarded = array();

	public function paciente(){
		return $this->belongsTo(
			'App\Models\Odontologia\Paciente',
			'id_paciente'
		);
	}

}
