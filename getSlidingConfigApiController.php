<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class getSlidingConfigApiController extends Controller {
    public function get_config(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        // connect to db
        $sliding_config = DB::connection('rheacache-db0')->table('sliding_config')->where('web_id', $web_id)->get(); // prevent SQL injection       
        return json_encode($sliding_config);
    }
}


