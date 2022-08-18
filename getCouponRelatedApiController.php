<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class getCouponRelatedApiController extends Controller {

    // fetch addfan status
    public function check_enable(Request $request){
        // $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        $web_id = $request->input('web_id');
        if (isset($web_id)) {
            // input web_id
            $addfan_status = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                                ->select('enable_addfan')
                                ->where('web_id', $web_id)->first(); // prevent SQL injection
            $addfan_status = isset($addfan_status) ? $addfan_status : array("enable_addfan"=> 0);
        } else {
            // no web_id
            $addfan_status = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                                ->select('web_id', 'enable_addfan')
                                ->get();
        }
        // connect to db
        // $tracking_config = DB::connection('rheacache-db0')->table('cdp_tracking_settings')->where('web_id', $web_id)->get(); // prevent SQL injection  
        // $addfan_status = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
        //                         ->select('enable_addfan')
        //                         ->where('web_id', $web_id)->first(); // prevent SQL injection
        // $addfan_status = isset($addfan_status) ? $addfan_status : array("enable_addfan"=> 0);
        return json_encode($addfan_status);
    }

    // change enable
    public function change_enable(Request $request){
        // $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        $web_id = $request->input('web_id');
        $enable = (int)$request->input('enable');
        if (!isset($enable)) {
            # no enable input
            return json_encode(-1);
        }
        if ($enable!=0 && $enable!=1) {
            # not valid input
            return json_encode(-2);
        }
        if (isset($web_id)) {
            // input web_id
            $affected = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                            ->where('web_id', $web_id)
                            ->update(['enable_addfan' => $enable]);
        } else {
            // no web_id
            $affected = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
                            ->where('enable_tracking', 2)
                            ->where('enable_analysis', 2)
                            ->update(['enable_addfan' => $enable]);
        }
        // dd($coupon_id);
        // connect to db, update to 0 or 1
        // $affected = DB::connection('rheacache-db0')->table('cdp_tracking_settings')
        //                         ->where('web_id', $web_id)
        //                         ->update(['enable_addfan' => $enable]);
        return json_encode($affected);
    }



    // fetch coupon status
    public function get_coupon_status(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        
        // connect to db, select coupon in running with the highest prioity
        $coupon_query = DB::connection('rhea1-db0')->table('addfan_activity')
                                // ->select('coupon_budget', 'start_time', 'end_time')
                                ->selectRaw('id, (coupon_budget-cost)/(1+datediff(end_time, curdate())) as avg_budget, 
                                datediff(end_time, curdate()) as remain_time, (coupon_budget-cost) as remain_budget, 
                                customer_type, website_type, max_revenue')                            
                                ->where('web_id', $web_id)
                                ->where('activity_enable', 1)
                                ->where('coupon_enable', 1)
                                ->where('activity_delete', '!=', 1)
                                ->where('coupon_budget', '>', 'cost')
                                ->whereRaw('curdate() between start_time and end_time')   
                                ->orderByRaw('avg_budget DESC, remain_time ASC')                          
                                ->first(); // prevent SQL injection
        $coupon_status = isset($coupon_query) ? true : false;
        $coupon_id = $coupon_status ? $coupon_query->id : -1;
        $coupon_customer_type = $coupon_status ? $coupon_query->customer_type : 0;
        $website_type = $coupon_status ? $coupon_query->website_type : 0;
        $max_revenue = $coupon_status ? $coupon_query->max_revenue : 0;

        $return_results = array("status" => $coupon_status, "id" => $coupon_id, "customer_type" => $coupon_customer_type,
                                "website_type" => $website_type, "max_revenue" => $max_revenue);
        // dd(json_encode($return_results));
        return json_encode($return_results);
    }
    // fetch all coupon status
    public function get_all_coupon_status(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        
        // connect to db, select coupon in running with the highest prioity
        $coupon_query = DB::connection('rhea1-db0')->table('addfan_activity')
                                // ->select('coupon_budget', 'start_time', 'end_time')
                                ->selectRaw('id, (coupon_budget-cost)/(1+datediff(end_time, curdate())) as avg_budget, 
                                datediff(end_time, curdate()) as remain_time, (coupon_budget-cost) as remain_budget, 
                                customer_type, website_type, coupon_limit, max_revenue, coupon_time_limit')
                                ->where('web_id', $web_id)
                                ->where('activity_enable', 1)
                                ->where('coupon_enable', 1)
                                ->where('activity_delete', '!=', 1)
                                ->where('coupon_budget', '>', 'cost')
                                ->whereRaw('curdate() between start_time and end_time')   
                                ->orderByRaw('avg_budget DESC, remain_time ASC')
                                ->get();

        // dd(empty($coupon_query[0]));
        $coupon_status = empty($coupon_query[0]) ? false : true;
        if ($coupon_status) {
            // $return_results = array();
            foreach ($coupon_query as $sub_array) {
                $sub_array->status = true;
                // array_push($return_results, $sub_array);
            }
            return json_encode($coupon_query);
        } else {
            $return_results = array("avg_budget"=>0, "remain_time"=>0, "remain_budget"=>0, "coupon_limit"=> 'limit-bill=0',
                                    "id" => -1, "customer_type" => 0, "website_type" => 0, "max_revenue" => 0, "status" => false);
            return json_encode(array($return_results));
        };
    }

    // fetch coupon model
    public function get_coupon_model(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : 'default';
        $coupon_id = null !==$request->input('coupon_id') ? $request->input('coupon_id') : 0;

        // connect to db
        $coupon_model = DB::connection('rheacache-db0')->table('cdp_tracking_settings')        
                                ->select('model_key_js', 'model_value', 'model_intercept')
                                ->where('web_id', $web_id)
                                ->first(); // prevent SQL injection
        if (!isset($coupon_model)) {
            $coupon_model = DB::connection('rheacache-db0')->table('cdp_tracking_settings')        
            ->select('model_key_js', 'model_value', 'model_intercept')
            ->where('web_id', 'default')
            ->first(); // prevent SQL injection
        }
        $coupon_bound = DB::connection('rhea1-db0')->table('addfan_activity')
                                ->select('upper_bound', 'lower_bound')
                                ->where('id', $coupon_id)
                                ->first(); // prevent SQL injection
        $model_key_js = isset($coupon_model) ? $coupon_model->model_key_js : "ps,t_p_t,c_c_t";
        $model_value = isset($coupon_model) ? $coupon_model->model_value : "0,0,0";
        $model_intercept = isset($coupon_model) ? $coupon_model->model_intercept : 0;
        $upper_bound = isset($coupon_bound) ? $coupon_bound->upper_bound : 1;
        $lower_bound = isset($coupon_bound) ? $coupon_bound->lower_bound : 1;
        $coupon_model_result = array("model_key_js"=>$model_key_js, "model_value"=>$model_value, "model_intercept"=>$model_intercept, 
                                    "upper_bound"=>$upper_bound, "lower_bound"=>$lower_bound);
        return json_encode($coupon_model_result);
    }
    // fetch coupon model
    public function get_coupon_model2(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : 'default';
        $coupon_id = null !==$request->input('coupon_id') ? $request->input('coupon_id') : 0;

        // connect to db
        $coupon_model = DB::connection('rheacache-db0')->table('cdp_tracking_settings')        
                                ->select('model_key_js', 'model_value', 'model_intercept')
                                ->where('web_id', $web_id)
                                ->first(); // prevent SQL injection
        if (!isset($coupon_model)) {
            $coupon_model = DB::connection('rheacache-db0')->table('cdp_tracking_settings')        
            ->select('model_key_js', 'model_value', 'model_intercept')
            ->where('web_id', 'default')
            ->first(); // prevent SQL injection
        }
        $coupon_bound = DB::connection('rhea1-db0')->table('addfan_activity')
                                ->select('upper_bound', 'lower_bound')
                                ->where('id', $coupon_id)
                                ->first(); // prevent SQL injection
        $model_key_js = isset($coupon_model) ? $coupon_model->model_key_js : "ps,t_p_t,c_c_t";
        $model_value = isset($coupon_model) ? $coupon_model->model_value : "0,0,0";
        $model_intercept = isset($coupon_model) ? $coupon_model->model_intercept : 0;
        $upper_bound = isset($coupon_bound) ? $coupon_bound->upper_bound : 1;
        $lower_bound = isset($coupon_bound) ? $coupon_bound->lower_bound : 1;
        $coupon_model_result = array("model_key_js"=>$model_key_js, "model_value"=>$model_value, "model_intercept"=>$model_intercept, 
                                    "upper_bound"=>$upper_bound, "lower_bound"=>$lower_bound);
        return json_encode($coupon_model_result);
    }

    // fetch coupon details
    public function get_coupon(Request $request){
        $coupon_id = null !==$request->input('coupon_id') ? $request->input('coupon_id') : 0;
        // dd($coupon_id);
        // connect to db
        $coupon = DB::connection('rhea1-db0')->table('addfan_activity')
                                ->join('addfan_coupon', 'addfan_activity.link_code', 'addfan_coupon.link_code')
                                ->select('addfan_activity.title', 'addfan_activity.coupon_description', 'addfan_coupon.coupon_code', 
                                'addfan_activity.link_code', 'addfan_activity.coupon_type', 'addfan_activity.coupon_amount', 
                                'addfan_activity.coupon_code_mode', 'addfan_activity.coupon_time_limit', 'addfan_activity.coupon_limit', 
                                'addfan_activity.coupon_url', 'addfan_activity.coupon_waitingTime')
                                ->where('addfan_activity.id', $coupon_id)
                                ->where('addfan_coupon.is_sent', 0)
                                ->first(); // prevent SQL injection        

        return json_encode($coupon);
    }

    // chnage addfan_table is_sent to 1
    public function chage_coupon_is_sent(Request $request){
        $link_code = null !==$request->input('link_code') ? $request->input('link_code') : 0;
        $coupon_code = null !==$request->input('coupon_code') ? $request->input('coupon_code') : 0;
        // dd($coupon_id);
        // connect to db
        $affected = DB::connection('rhea1-db0')->table('addfan_coupon')
                                ->where('link_code', $link_code)
                                ->where('coupon_code', $coupon_code)
                                ->update(['is_sent' => 1]);
        return json_encode($affected);
    }

    // fetch ad status
    public function get_ad_status(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';        
        // connect to db, select coupon in running with the highest prioity
        $ad_query = DB::connection('rhea1-db0')->table('addfan_activity')
                                ->selectRaw('id, datediff(end_time, curdate()) as remain_time, website_type, customer_type')
                                ->where('web_id', $web_id)
                                ->where('activity_enable', 1)
                                ->where('ad_enable', 1)
                                ->where('activity_delete', '!=', 1)
                                ->whereRaw('curdate() between start_time and end_time')
                                ->orderByRaw('remain_time ASC')
                                ->first(); // prevent SQL injection
        $ad_status = isset($ad_query) ? true : false;
        $ad_id = $ad_status ? $ad_query->id : -1;
        $website_type = $ad_status ? $ad_query->website_type : 0;
        $customer_type = $ad_status ? $ad_query->customer_type : 0;
        $return_results = array("status" => $ad_status, "id" => $ad_id, "website_type"=> $website_type, "customer_type"=> $customer_type);
        // dd(json_encode($return_results));
        return json_encode($return_results);
    }
    // fetch all coupon status
    public function get_all_ad_status(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        
        // connect to db, select coupon in running with the highest prioity
        $ad_query = DB::connection('rhea1-db0')->table('addfan_activity')
                                ->selectRaw('id, datediff(end_time, curdate()) as remain_time, website_type, customer_type')
                                ->where('web_id', $web_id)
                                ->where('activity_enable', 1)
                                ->where('ad_enable', 1)
                                ->where('activity_delete', '!=', 1)
                                ->whereRaw('curdate() between start_time and end_time')
                                ->orderByRaw('remain_time ASC')
                                ->get(); // prevent SQL injection

        // dd(empty($coupon_query[0]));
        $ad_status = empty($ad_query[0]) ? false : true;
        if ($ad_status) {
            $return_results = array();
            foreach ($ad_query as $sub_array) {
                $sub_array->status = true;
                array_push($return_results, $sub_array);
            }
            return json_encode($ad_query);
        } else {
            $return_results = array("id" => -1, "customer_type" => 0, "website_type" => 0, "status" => false);
            return json_encode(array($return_results));
        };
    }
    // fetch ad details
    public function get_ad(Request $request){
        $ad_id = null !==$request->input('ad_id') ? $request->input('ad_id') : 0;
        // connect to db
        $ad = DB::connection('rhea1-db0')->table('addfan_activity')
                                ->select('ad_image_url', 'ad_url', 'ad_btn_url', 'ad_btn_text', 'ad_btn_color')
                                ->where('id', $ad_id)
                                ->first(); // prevent SQL injection

        $ad_image_url = isset($ad) ? $ad->ad_image_url : "_";
        $ad_url = isset($ad) ? $ad->ad_url : "_";
        $ad_btn_url = isset($ad) ? $ad->ad_btn_url : "_";
        $ad_btn_text = isset($ad) ? $ad->ad_btn_text : "_";
        $ad_btn_color = isset($ad) ? $ad->ad_btn_color : "_";
        $ad_result = array("ad_image_url"=>$ad_image_url, "ad_url"=>$ad_url, "ad_btn_url"=>$ad_btn_url, 
                                    "ad_btn_text"=>$ad_btn_text, "ad_btn_color"=>$ad_btn_color);        
        return json_encode($ad_result);
    }

    // fetch sale items
    public function get_sale_item(Request $request){
        $web_id = null !==$request->input('web_id') ? $request->input('web_id') : '_';
        $price = null !==$request->input('price') ? $request->input('price') : 0;

        // connect to db
        $sale_item = DB::connection('rhea1-db0')->table('item_list')
                                ->select('price', 'sale_price', 'title', 'url')
                                ->where('web_id', $web_id)
                                ->where('sale_price', '>=', $price)
                                ->whereRaw('price - sale_price > 0')
                                ->orderby('sale_price')
                                ->first(); // prevent SQL injection
        if (!isset($sale_item)) {
            $sale_item = DB::connection('rhea1-db0')->table('item_list')
                                ->select('price', 'sale_price', 'title', 'url')
                                ->where('web_id', $web_id)
                                ->whereRaw('price - sale_price > 0')
                                ->orderby('sale_price', 'desc')
                                ->first(); // prevent SQL injection
        }
        $title = isset($sale_item) ? $sale_item->title : "_";
        $url = isset($sale_item) ? $sale_item->url : "_";
        $sale_item_result = array("title"=>$title, "url"=>$url);
        return json_encode($sale_item_result);
    }
}

