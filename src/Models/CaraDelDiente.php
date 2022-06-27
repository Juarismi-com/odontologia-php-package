<?php

namespace App\Models\Odontologia;

use Illuminate\Database\Eloquent\Model;

class CaraDelDiente extends Model
{
    protected $table = 'odont_caradiente';
	protected $guarded = array();


	public function paciente(){
	 	return $this->belongsTo(
			'App\Models\Negocio\Cliente',
			'id_cliente',
			'id'
		);		
	}

}
