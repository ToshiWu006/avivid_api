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
    // USE THIS
    public function get_config2(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        // connect to db
        // $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')->where('web_id', $web_id)->get(); // prevent SQL injection  
        
        $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                                ->select('gtm_code', 'addToCart_trigger_name', 'addToCart_index', 'addToCart_key', 
                                'removeCart_trigger_name', 'removeCart_index', 'removeCart_key', 
                                'purchase_trigger_name', 'purchase_index', 'purchase_key', 'purchase_choose_index', 'enable_tracking',
                                'coupon_input_trigger', 'coupon_input_selector', 'coupon_click_selector', 'coupon_enter_href')
                                ->where('web_id', $web_id)->first(); // prevent SQL injection
        $tracking_config = isset($tracking_config) ? $tracking_config : array("enable_tracking"=> 0);

        return json_encode($tracking_config);
    }


    // DEPRECATED
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

    // fetch cart related parse config
    public function get_cart_parser(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : 'default';
        // connect to db
        $cart_parser = DB::connection('rheacache-db0')->table('cdp_tracking_settings')        
        ->select('parsed_addCart_key', 'parsed_addCart_key_rename', 'parsed_removeCart_key', 'parsed_removeCart_key_rename')
        ->where('web_id', $web_id)
        ->first(); // prevent SQL injection
        
        if (!isset($cart_parser)) {
            $results = array("addCart"=>array(), "removeCart"=>array());
            return json_encode($results);
        }

        $enable_addCart_parser = $cart_parser->parsed_addCart_key!='_' ? true : false;
        $enable_removeCart_parser = $cart_parser->parsed_removeCart_key!='_' ? true : false;
        $enable_rename_parser = $cart_parser->parsed_addCart_key_rename!='_' ? true : false;


        $addCart_key_array = explode(',', $cart_parser->parsed_addCart_key);
        $Cart_key_rename_array = explode(',', $cart_parser->parsed_addCart_key_rename);
        $removeCart_key_array = explode(',', $cart_parser->parsed_removeCart_key);
        $results_addCart = array();
        $results_removeCart = array();
        if ($enable_rename_parser) {
            for ($i=0; $i<count($Cart_key_rename_array); $i++) {

                $key = $Cart_key_rename_array[$i];
                if ($key=='product_id' || $key=='product_name' || $key=='product_price' || $key=='product_quantity') {
                    if ($enable_addCart_parser) {
                        $results_addCart[$key] = $addCart_key_array[$i];
                    }
                    if ($enable_removeCart_parser) {
                        $results_removeCart[$key] = $removeCart_key_array[$i];
                    }
                }
            }
        }
        $results = array("addCart"=>$results_addCart, "removeCart"=>$results_removeCart);
        return json_encode($results);
    }



}

