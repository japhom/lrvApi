<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;

class ticketController extends Controller
{
    public function index (Request $request,$fecha)
    {
        $sql =
        'SELECT
            crr_id,rta_nombre,rta_origen,rta_destino,rta_horaSalida,rta_precio,
            vhc_nombre,vhc_placas,vhc_descripcion,
            ecr_nombre,blt_number,blt_id,ebl_id,ebl_nombre,blt_cliente
        FROM transports.Corrida C
        INNER JOIN transports.Boletos USING ( crr_id)
        INNER JOIN transports.cat_EstadoBoleto USING (ebl_id)
        INNER JOIN transports.cat_EstadoCorrida USING( ecr_id)
        INNER JOIN transports.cat_Ruta USING ( rta_id )
        INNER JOIN transports.Vehiculos USING (vhc_id)
        WHERE C.crr_fecha =DATE("'.$fecha.'")
        ';
        $t = DB::select($sql);

        $res = new \stdClass();
        $res->asientos = [];
        foreach ($t as $r) {
            if (!isset($res->idcorrida)) {
                $res->idcorrida = $r->crr_id;
                $res->rutaNombre= $r->rta_nombre;
                $res->origen    = $r->rta_origen;
                $res->destino   = $r->rta_destino;
                $res->vehiculo  = $r->vhc_descripcion;
                $res->estado    = $r->ecr_nombre;
                $res->placas    = $r->vhc_placas;
                $res->economico = $r->vhc_nombre;
                $res->salida    = $r->rta_horaSalida;
            }
            $res->asientos[$r->blt_number]= new \stdClass();
            $res->asientos[$r->blt_number]->status  = $r->ebl_nombre;
            $res->asientos[$r->blt_number]->number  = $r->blt_number;
            $res->asientos[$r->blt_number]->idBoleto= $r->blt_id;
            $res->asientos[$r->blt_number]->cliente = $r->blt_cliente;
            $res->asientos[$r->blt_number]->precio  = $r->rta_precio;
        }
         return response(json_encode($res), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
    public function nuevaCorrida(Request $re,Carbon $fecha,$rta,$vhc)
    {
        $res = new \stdClass();
        $res->status = "ok";

        $values  = array(array("rta_id"=>$rta,"ecr_id"=>"1","vhc_id"=>$vhc,"crr_fecha"=>$fecha));
        $ins = DB::table("transports.Corrida")->insert($values);
        $res->idcorrida = $ins;

        return response(json_encode($res), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
    public function getVehiculos()
    {
        $sql =
        'SELECT * FROM transports.Vehiculos ';
        $t = DB::select($sql);

        $res = new \stdClass();
        $res->autos = [];
        foreach ($t as $r) {
            $v = new \stdClass();
            $v->id = $r->vhc_id;
            $v->nombre = $r->vhc_marca ." ".$r->vhc_nombre . " ".$r->vhc_descripcion;
            array_push($res->autos,$v);
        }
        return response(json_encode($res), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
    public function getRutas()
    {
        $sql =
        'SELECT * FROM transports.cat_Ruta ';
        $t = DB::select($sql);

        $res = new \stdClass();
        $res->rutas = [];
        foreach ($t as $r) {
            $v = new \stdClass();
            $v->id = $r->rta_id;
            $v->nombre = $r->rta_origen ." - ".$r->rta_destino."[ ".$r->rta_horaSalida."]";
            array_push($res->rutas,$v);
        }
        return response(json_encode($res), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
    public function storeSit(Request $re,$icr,$client,$sit)
    {
        $res = new \stdClass();
        $res->post = $re->all();
        $res->status = "error";
        $res->id = $icr;
        $res->nombre = $client;
        $res->asiento = $sit;
        return response(json_encode($res), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
}
