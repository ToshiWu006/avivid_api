<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class getTrackingConfigApiController extends Controller {

    public function check_enable(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        // connect to db
        // $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')->where('web_id', $web_id)->get(); // prevent SQL injection  
        
        $tracking_status = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                                ->select('enable_tracking')
                                ->where('web_id', $web_id)->first(); // prevent SQL injection
        $tracking_status = isset($tracking_status) ? $tracking_status : array("enable_tracking"=> 0);

        return json_encode($tracking_status);
    }

    public function get_config2(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        // connect to db
        // $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')->where('web_id', $web_id)->get(); // prevent SQL injection  
        
        $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                                ->select('gtm_code', 'addToCart_trigger_name', 'addToCart_index', 'addToCart_key', 
                                'removeCart_trigger_name', 'removeCart_index', 'removeCart_key', 
                                'purchase_trigger_name', 'purchase_index', 'purchase_key', 'enable_tracking')
                                ->where('web_id', $web_id)->first(); // prevent SQL injection
        $tracking_config = isset($tracking_config) ? $tracking_config : array("enable_tracking"=> 0);

        return json_encode($tracking_config);
    }



    public function get_config(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        // connect to db
        // $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')->where('web_id', $web_id)->get(); // prevent SQL injection  
        
        $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                                ->select('gtm_code', 'addToCart_trigger_name', 'addToCart_index', 'addToCart_key', 
                                'removeCart_trigger_name', 'removeCart_index', 'removeCart_key', 
                                'purchase_trigger_name', 'purchase_index', 'purchase_key', 'enable_tracking')
                                ->where('web_id', $web_id)->get(); // prevent SQL injection  

        return json_encode($tracking_config);
    }
}

