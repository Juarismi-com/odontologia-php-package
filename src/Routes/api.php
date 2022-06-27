<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::name('jOdontologia.')
	->namespace('Juarismi\Odontologia\Http\Controllers')
	->middleware(['api', 'cors'])
	->prefix('odontologia')
	->group(function(){


	// -- Dientes --
	Route::post(
		'pacientes/{paciente}/dientes', 'PacienteController@updateDiente')
		->name('pacientes.guardar-diente');
	Route::get(
		'pacientes/{paciente}/dientes', 'PacienteController@getDienteList')
		->name('pacientes.listado-de-dientes');
	Route::post(
		'pacientes/{paciente}/diente-caras', 
		'PacienteController@updateDienteCara')
		->name('pacientes.guardar-diente-cara');
	Route::get(
		'pacientes/{paciente}/diente-caras', 
		'PacienteController@getDienteCara')
		->name('pacientes.listado-de-caras');


	// -- Ficha / Antecedentes / Diagnostico --
	Route::post(
		'pacientes/{id}/ficha', 'PacienteController@updateFicha')
		->name('pacientes.ficha');	
	Route::post(
			'pacientes/{id}/antecedentes', 
			'PacienteController@updateAntecedentes'
		)->name('pacientes.antecedentes');	
	Route::post(
			'pacientes/{id}/plan-diagnostico', 
			'PacienteController@updateDiagnostico'
		)->name('pacientes.plan-diagnostico');	

	// -- Pacientes --
	Route::delete(
		'pacientes/{paciente}/archivar', 'PacienteController@archivar')
		->name('pacientes.archivar');

	Route::apiResources([
		'pacientes' => 'PacienteController',
	]);


});