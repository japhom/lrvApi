<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class boardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sql =
        'SELECT
            b.id as idBoard,
            b.name AS BoardName,
            internalName,
            i.id AS ItemId,
            i.name as ItemName
        FROM Board b
        LEFT JOIN BoardItems i ON  b.id = i.idBoard
        ORDER BY b.id
        ';
        $t = DB::select($sql);
        // $res = new \stdClass();
        $res = array();
        $name = null;
        foreach ($t as $r) {
            if ( $name == null ) {
                $name = new \stdClass();
                $name->idBoard = $r->idBoard;
                $name->input ='';// new \stdClass();
                // $name->input->add = '';
                // $name->input->remove = '';
                $name->title = $r->BoardName;
                $name->index = "0";
                $name->items = [];
            }
            if ( is_object($name ) && $name->title != $r->BoardName) {
                array_push($res,$name);
                $name = new \stdClass();
                $name->idBoard = $r->idBoard;
                $name->input = '' ;//new \stdClass();
                // $name->input->add = '';
                // $name->input->remove = '';
                $name->title = $r->BoardName;
                $name->index = "0";
                $name->items = [];
            }
            array_push($name->items,$r->ItemName);
        }
        array_push($res,$name);
        return response(json_encode($res), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
    public function saveItem(Request $request,$board,$data)
    {
        $datos = array("name"=>$data,"index"=>1,"idBoard"=>$board);
        $t = DB::table("BoardItems")->insert($datos);
        $res = new \stdClass();
        $res->status = "ok";
        return response(json_encode( $res ), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }

    public function addBoard(Request $request,$board)
    {
        $datos = array("name"=>$board,'internalName' => mb_strtolower($board));
        $t = DB::table("Board")->insert($datos);
        $res = new \stdClass();
        $res->status = "ok";
        return response(json_encode( $res ), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }

    public function deleteItem(Request $request,$board,$item)
    {

        DB::table('BoardItems')->where([
            ["idBoard","=",$board],
            ['name',"=",$item]
        ])->delete();
        $res = new \stdClass();
        $res->status = "ok";
        return response(json_encode( $res ), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }

    public function deleteBoard(Request $request,$board)
    {
        DB::table('BoardItems')->where("idBoard","=",$board)->delete();
        DB::table('Board')->where("id","=",$board)->delete();

        $res = new \stdClass();
        $res->status = "ok";
        return response(json_encode( $res ), 200)->header('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
}
