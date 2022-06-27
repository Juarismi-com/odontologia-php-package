<?php

namespace Juarismi\Odontologia\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Negocio\Funcionario;
use App\Model\Odontologia\Paciente;
use App\Model\Odontologia\FichaTecnica;
use App\Http\Requests\Cliente\ClienteRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Resources\Cliente\ClienteResource;
//use App\Http\Resources\Cobranza\ClienteCobranzaResource;
use \Carbon\Carbon;
use App\Model\Odontologia\Diente;
use App\Model\Odontologia\CaraDelDiente;


class PacienteController extends Controller
{

    public function __construct(){
        $this->middleware('cors');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {      
        // Filtos
        $estado =  $request->input('estado','activo');
        $clienteId = $request->input('id_cliente', NULL);
        
        // Pagination
        $rows = $request->input('rows', 20);

        // Orders
        $orderBy = $request->input('order_by', 'created_at');
        $orderType = $request->input('order_type', 'desc');

        $clienteList = Paciente::where('estado', $estado);   

        if (isset($clienteId) && is_numeric($clienteId))
            $clienteList->where('id_cliente', $clienteId);

        return $clienteList->orderBy($orderBy, $orderType)
                    ->paginate($rows);
    }



    /**
     * Genera un codigo aleatorio
     */
    protected function gen_codigo(){
        $codigo = sha1(\Carbon\Carbon::now());
        $codigo = strtoupper(substr($codigo, 0, 10));
        return $codigo;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClienteRequest $request)
    {

        $cliente = new Paciente;
        $cliente->estado = "activo";
        $cliente->nombre = urldecode($request->input('nombre'));
        $cliente->apellido = urldecode($request->input('apellido'));
        $cliente->celular = urldecode($request->input('celular'));
        $cliente->ci = urldecode($request->input('ci'));
        $cliente->telefono = urldecode($request->input('telefono'));
        $cliente->ruc = urldecode($request->input('ruc'));
        $cliente->email = urldecode($request->input('email'));
        $cliente->latitud = $request->input('latitud');
        $cliente->longitud = $request->input('longitud');
        $cliente->codigo = "CLI-" . $this->gen_codigo();
        $cliente->referencia_geo = urldecode(
            $request->input('referencia_geo')
        );
        $cliente->tipo_cliente = $request->input('tipo_cliente');
        $cliente->tipo_identificador = $request->input('tipo_identificador');
        $cliente->estado = $request->input('estado','activo');
        $cliente->edad = $request->input('edad');
        $cliente->genero = $request->input('genero');


        // Valida la fecha de nacimiento
        $fechaNacimiento = $request->input('fecha_nacimiento', NULL);
        if($fechaNacimiento != NULL){
            $fechaNacimiento = \Carbon\Carbon::createFromFormat(
                'd/m/Y', urldecode($fechaNacimiento)
            )->toDateString();

            $cliente->fecha_nacimiento = $fechaNacimiento;
        }


        $cliente->save();
        $cliente->ficha()->create();
        
        return [
            "data" => $cliente,
            "message" => "Paciente guardado correctamente"
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cliente = Paciente::with('ficha')->find($id);

        if(!isset($cliente)){
            return response([
                'errors' => [ 'Paciente no encontrado o inactivo']
            ], 404);    
        }

        return [
            "data" => $cliente,
            "type" => "paciente"
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClienteRequest $request, $id)
    {
        $cliente = Paciente::find($id);
        if(!isset($cliente)){
            return response([
                'errors' => [ 'Paciente no encontrado o inactivo']
            ], 404);    
        }

        $cliente->nombre = urldecode(
            $request->input('nombre', $cliente->nombre)
        );
        $cliente->apellido = urldecode(
            $request->input('apellido', $cliente->apellido)
        );
        $cliente->celular = urldecode(
            $request->input('celular', $cliente->celular)
        );
        $cliente->ci = urldecode(
            $request->input('ci', $cliente->ci)
        );
        $cliente->telefono = urldecode(
            $request->input('telefono', $cliente->telefono)
        );
        $cliente->ruc = urldecode(
            $request->input('ruc', $cliente->ruc)
        );
        $cliente->email = urldecode(
            $request->input('email', $cliente->email)
        );
        $cliente->latitud = $request->input('latitud', $cliente->latitud);
        $cliente->longitud = $request->input('longitud', $cliente->longitud);
        $cliente->referencia_geo = urldecode(
            $request->input('referencia_geo', $cliente->referencia_geo)
        );
        $cliente->tipo_cliente = $request->input(
            'tipo_cliente', $cliente->tipo_cliente
        );
        
        $cliente->estado = $request->input('estado', $cliente->estado);
        $cliente->edad = $request->input('edad', $cliente->edad);
        $cliente->genero = $request->input('genero', $cliente->genero);


        // Valida la fecha de nacimiento
        $fechaNacimiento = $request->input('fecha_nacimiento', NULL);
        if($fechaNacimiento != NULL){
            $fechaNacimiento = \Carbon\Carbon::createFromFormat(
                'd/m/Y', urldecode($fechaNacimiento)
            )->toDateString();

            $cliente->fecha_nacimiento = $fechaNacimiento;
        }
        
        $cliente->save();

        return [
            "data" => $cliente,
            "message" => "Paciente actualizado correctamente"
        ];
    }





    /**
     * Actualiza la ficha odontologica de un paciente/cliente
     * 
     * @method POST
     * @link /pacientes/{id}/ficha
     * 
     * @param Request $request
     * @param number $id - 
     * 
     */
    public function updateFicha(Request $request , $id){
        $cliente = Paciente::find($id);
        $estado = $request->input('estado', 'activo');

        if (!isset($cliente->ficha)){
            return response([
                'errors' => [ 'Paciente o ficha no encontrada' ]
            ], 404);
        }
        
        $ficha = $cliente->ficha;
        $ficha->motivo = urldecode($request->input('motivo'));
        $ficha->cara = urldecode($request->input('cara'));
        $ficha->labios_comisura = urldecode(
            $request->input('labios_comisura')
        );

        $ficha->ganglios = urldecode($request->input('ganglios'));
        $ficha->atm = urldecode($request->input('atm'));
        $ficha->estado = $estado;
        $ficha->region_hioidea_tiroidea = urldecode(
            $request->input('region_hioidea_tiroidea')
        );
        $ficha->ex_clinico_otros = urldecode(
            $request->input('ex_clinico_otros')
        );
        $ficha->ex_carrillos = urldecode($request->input('ex_carrillos'));
        $ficha->ex_mucosa = urldecode($request->input('ex_mucosa'));
        $ficha->ex_encia = urldecode($request->input('ex_encia'));
        $ficha->ex_lengua = urldecode($request->input('ex_lengua'));
        $ficha->ex_paladar = urldecode($request->input('ex_paladar'));
        
        $ficha->ex_comple_otros = urldecode(
            $request->input('ex_comple_otros')
        );
        $ficha->ex_comple_rx = urldecode($request->input('ex_comple_rx'));
        $ficha->ex_comple_ortopantomografia = urldecode(
            $request->input('ex_comple_ortopantomografia')
        );
        $ficha->observaciones = urldecode(
            $request->input('observaciones')
        );
        $ficha->ex_comple_lateral_craneo = urldecode(
            $request->input('ex_comple_lateral_craneo')
        );
        $ficha->ex_comple_frontal = urldecode(
            $request->input('ex_comple_frontal')
        );
        $ficha->ex_comple_periapical = urldecode(
            $request->input('ex_comple_periapical')
        );
        $ficha->diagnostico_plan = urldecode(
            $request->input('diagnostico_plan')
        );

        $ficha->save();

        return [
            "data" => $ficha,
            "message" => "Ficha actualizada correctamente"
        ];  

    }

    /**
     * Actualiza el plan de tratamientieno / diagnostico_plan
     * 
     * @method POST
     * @link /pacientes/{id}/plan-diagnostico
     * 
     */
    public function updateDiagnostico(Request $request , $id){

        $request->validate([
            'diagnostico_plan' => 'required'
        ]);

        $cliente = Paciente::find($id);

        if (!isset($cliente->ficha)){
            return response([
                'errors' => [ 'Paciente o ficha no encontrada' ]
            ], 404);
        }

        
        $ficha = $cliente->ficha;
        $ficha->diagnostico_plan = urldecode(
            $request->input('diagnostico_plan')
        );
        $ficha->save();

        return [
            "data" => $ficha,
            "message" => "Diagnostico actualizado correctamente"
        ];  

    }


    /**
     * Actualiza los antecedentes de un paciente
     * 
     * @method POST
     * @link /pacientes/{cliente_id}/antecedentes
     */
    public function updateAntecedentes(Request $request, $id){
        $cliente = Paciente::find($id);
        $estado = $request->input('estado', 'activo');

        if (!isset($cliente->ficha)){
            return response([
                'errors' => [ 'Paciente o ficha no encontrada' ]
            ], 404);
        }
        
        $ficha = $cliente->ficha;
        $ficha->ant_tratamiento_actual = urldecode(
            $request->input('ant_tratamiento_actual')
        );
        $ficha->ant_consume_medicamento = urldecode(
            $request->input('ant_consume_medicamento')
        );
        $ficha->ant_cx = urldecode(
            $request->input('ant_cx')
        );
        $ficha->ant_transfucion_sanguinea = urldecode(
            $request->input('ant_transfucion_sanguinea')
        );
        $ficha->ant_consume_droga = urldecode(
            $request->input('ant_consume_droga')
        );
        $ficha->ant_alergico_penicilina = urldecode(
            $request->input('ant_alergico_penicilina')
        );
        $ficha->ant_alergico_anestecia = urldecode(
            $request->input('ant_alergico_anestecia')
        );
        $ficha->ant_alergico_aspirina = urldecode(
            $request->input('ant_alergico_aspirina')
        );
        $ficha->ant_presion_arterial = urldecode(
            $request->input('ant_presion_arterial')
        );
        $ficha->ant_sangra_mucho = urldecode(
            $request->input('ant_sangra_mucho')
        );
        $ficha->ant_problema_sanguineo = urldecode(
            $request->input('ant_problema_sanguineo')
        );
        $ficha->ant_posee_vih = urldecode(
            $request->input('ant_posee_vih')
        );
        $ficha->ant_toma_retroviral = urldecode(
            $request->input('ant_toma_retroviral')
        );
        $ficha->ant_esta_embarazada = urldecode(
            $request->input('ant_esta_embarazada')
        );
        $ficha->ant_consume_anticonceptivo = urldecode(
            $request->input('ant_consume_anticonceptivo')
        );
        $ficha->ant_enfermedad_venerea = urldecode(
            $request->input('ant_enfermedad_venerea')
        );
        $ficha->ant_problema_cardiado = urldecode(
            $request->input('ant_problema_cardiado')
        );
        $ficha->ant_hepatitis = urldecode(
            $request->input('ant_hepatitis')
        );
        $ficha->ant_fibre_reumatica = urldecode(
            $request->input('ant_fibre_reumatica')
        );
        $ficha->ant_asma = urldecode(
            $request->input('ant_asma')
        );
        $ficha->ant_diabete = urldecode(
            $request->input('ant_diabete')
        );
        $ficha->ant_ulcera_gastrica = urldecode(
            $request->input('ant_ulcera_gastrica')
        );
        $ficha->ant_tiroides = urldecode(
            $request->input('ant_tiroides')
        );
        $ficha->ant_le_cuesta_boca = urldecode(
            $request->input('ant_le_cuesta_boca')
        );
        $ficha->ant_ruidos_mandibula = urldecode(
            $request->input('ant_ruidos_mandibula')
        );
        $ficha->ant_herpes = urldecode(
            $request->input('ant_herpes')
        );
        $ficha->ant_muerde_unha = urldecode(
            $request->input('ant_muerde_unha')
        );
        $ficha->ant_fuma = urldecode(
            $request->input('ant_fuma')
        );
        $ficha->ant_cantidad_cigarillos = urldecode(
            $request->input('ant_cantidad_cigarillos')
        );
        $ficha->ant_consume_citricos = urldecode(
            $request->input('ant_consume_citricos')
        );
        $ficha->ant_muerde_objetos = urldecode(
            $request->input('ant_muerde_objetos')
        );
        $ficha->ant_bruxomano = urldecode(
            $request->input('ant_bruxomano')
        );

        $ficha->save();

        return [
            "data" => $ficha,
            "message" => "Antecedentes actualizados correctamente"
        ];  
    }


    /**
     * @
     * 
     * Guarada la geoubicacion de un cliente
     * @param  \Illuminate\Http\Request  $request
     */
    public function guardarGeoubicacion(Request $request, Cliente $cliente){
        
        $request->validate([
            'latitud' => 'required',
            'longitud' => 'required',
            'ciudad' => 'nullable',
            'provincia' => 'nullable',
            'referencias' => 'nullable'
        ]);

        $cliente->latitud = $request->input('latitud');
        $cliente->longitud = $request->input('longitud');

        $cliente->save();

        return [
            "data" => $cliente,
            "message" => "Paciente actualizado correctamente"
        ];
    }


    /**
     * Busca un cliente por DNI(RUC, Codigo o )
     */
    public function getClientePorDNI(Request $request, $dni){
        $cliente = Cliente::orWhere([
            'ruc' => $dni,
            'ci' => $dni,
            'codigo' => $dni
        ])->first();

        if (!isset($cliente))
            return [ "errors" => [ "Paciente no existe" ] ];   

        return [ "data" => $cliente ];    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!isset($cliente)){
            return response([
                'errors' => [
                    'Paciente no encontrado'
                ]
            ]);
        }

        return [
            "data" => $cliente,
            "message" => "Paciente eliminado correctamente"
        ];
    }


    /**
     * Busca coincidencias de clientes con los siguietnes campos, 
     * Solamente visualiza clientes activos
     */
    public function buscarClientes(Request $request){
        $q = $request->input("data", NULL);
        $estado =  isset($request->estado) ? $request->estado : 'activo';

        if ($q != NULL){
            $q = strtolower($q);

            $clienteList = Cliente::where('estado', $estado);
            $clienteList->orWhere(function($query) use ($data, $estado)
            {
                $query->orWhereRaw("LOWER(nombre) LIKE '%$q%'");
                $query->orWhereRaw("LOWER(apellido) LIKE '%$q%'");
                $query->orWhere([
                    'codigo' => $data,
                    'ruc' => $data,
                    'ci' => $data
                ])->where('estado', $estado);
            });
            
            return $clienteList->paginate(20); 
        } 
        
        return [
            "data" => [],
            "message" => "Ingrese nombre, ruc o código del paciente"
        ];
    }


    /**
     * Establece cliente como archivado o eliminado, sin la necesidad de 
     * eliminar el registro
     */
    public function archivar(Request $request, $id){
        $cliente = Cliente::find($id);

        if (!isset($cliente)){
            return response([ "errors" => [ "Paciente no existe" ]], 404);
        }

        $cliente->estado = 'archivado';
        $cliente->save();

        return [
            "data" => $cliente, 
            "message" => "Paciente archivado correctamente"
        ];
    }


    /**
     * Inserta o Actualiza SI tiene o NO un diente
     * 
     * @method POST
     * @link pacientes/{id}/dientes
     * 
     * @param $request
     * @param $id - Id del Paciente
     */
    public function updateDiente(Request $request, $id){
        $request->validate([
            'diente_nro' => 'required|integer|max:85',
            'tiene_diente' => 'nullable'
        ]);

        $cliente = Paciente::find($id);
        
        if (!isset($cliente)){
            return response([
                'errors' => [ 'Paciente no encontrado' ]
            ], 404);
        }

        $dienteNro = $request->input('diente_nro', NULL); 
        $diente = Diente::updateOrCreate(
            [ 'id_paciente' => $id, 'diente_nro' => $dienteNro ],
            [ 
                'tiene_diente' => $request->input('tiene_diente', NULL) ,
                'diente_nro' => $dienteNro,
                'id_paciente' => $id
            ]
        );

        return [
            "diente" => $diente,
            "message" => "Odontograma actualizado correctamente"
        ];
    }


    /**
     * Listado de dientes que el paciente NO TIENE (por defecto)
     * 
     * @method GET
     * @link pacientes/{id}/dientes
     * 
     * @param $request
     * @param $id - Id del Paciente
     */
    public function getDienteList(Request $request, $id){
        // Filtros
        $tieneDiente = $request->input('tiene_diente', 'no');

        $cliente = Paciente::find($id);
        if (!isset($cliente)){
            return response([
                'errors' => [ 'Paciente no encontrado' ]
            ], 404);
        }

        $dientes = Diente::where('id_paciente', $id)
            ->where('tiene_diente', $tieneDiente)
            ->get();

        return [
            "data" => $dientes
        ];
    }



    /**
     * Inserta los estados de las caras de un diente
     * 
     * @method POST
     * @link pacientes/{id}/diente-caras
     * 
     * @param $request
     * @param $id - Id del Paciente
     */
    public function updateDienteCara(Request $request, $id){
        $request->validate([
            'diente_nro' => 'required|integer|max:85',
            'diente_cara' => 'required|string',
            'diente_estado' => 'required|string'
        ]);

        $cliente = Paciente::find($id);
        if (!isset($cliente)){
            return response([
                'errors' => [ 'Paciente no encontrado' ]
            ], 404);
        }

        $datos = [ 
            'diente_nro' => $request->input('diente_nro'),
            'diente_cara' => $request->input('diente_cara'),
            'diente_estado' => $request->input('diente_estado'),
            'id_paciente' => $id
        ];
        $diente = CaraDelDiente::updateOrCreate(
            $datos, $datos
        );

        return [
            "diente" => $diente,
            "message" => "Odontograma actualizado correctamente"
        ];
    }


    /**
     * Listado de Cara de Dientes, Trae los 20 cambios más recientes
     * 
     * @method GET
     * @link pacientes/{id}/diente-caras
     * 
     * @param $request
     * @param $id - Id del Paciente
     */
    public function getDienteCara(Request $request, $id){
        $dienteEstado = $request->input('diente_estado', NULL);
        $dienteNro = $request->input('diente_nro', NULL);

        $cliente = Paciente::find($id);
        if (!isset($cliente)){
            return response([
                'errors' => [ 'Paciente no encontrado' ]
            ], 404);
        }

        $caras = CaraDelDiente::where('id_paciente', $id)
            ->orderBy('updated_at','desc');

        if ($dienteEstado != NULL)
            $caras->where('diente_estado', $dienteEstado);

        if ($dienteNro != NULL)
            $caras->where('diente_nro', $dienteNro);

        return [
            "data" => $caras->get()->groupBy(['diente_nro', 'diente_cara'])
        ];
    }


    /**
     * @method POST
     * @link /pacientes/{id}/diagnostico-plan
     */
    public function guardar_plan_de_diagnostico(Resquest $request){
        $cliente = Paciente::find($id);
        if (!isset($cliente)){
            return response([
                'errors' => [ 'Paciente no encontrado' ]
            ], 404);
        }

        $ficha = $cliente->ficha;
        $ficha->diagnostico_plan = urldecode($request->diagnostico_plan);
        $ficha->save();


        return [
            'data' => $ficha, 
            'message' => 'Ficha agregada correctamente'
        ];
    }   

}
