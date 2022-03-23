<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/itemPage/{web_id?}/{module_source?}/{keyword?}',
    ['uses' => 'itemPageController@index']
);
Route::post('/itemPageDomain/{web_id?}/{module_source?}/{keyword?}/{domain?}',
    ['uses' => 'itemPageDomainController@index']
);
Route::get('/searchEngineCdn', 'searchEngineController@searchEngine_cdn');

Route::get('/recommendWordCdn', 'searchEngineController@getRecommendWordCdn');

Route::get('/getHotWordCdn', 'searchEngineController@getHotWordCdn');
Route::get('/getAlsoWatchedWordCdn', 'searchEngineController@getAlsoWatchedWordCdn');
Route::get('/getUuidLikeWordCdn', 'searchEngineController@getUuidLikeWordCdn');

Route::post('/getHotWordCdnOutside', 'searchEngineController@getHotWordCdnOutside');
Route::post('/searchEngineCdnOutside', 'searchEngineController@searchEngine_cdnOutside');

Route::get('/ecomEmbeddedRecommendation', 'searchEngineControllerSEO@ecomEmbeddedRecommendation_v2');
Route::get('/ecomEmbeddedRecommendation_v2', 'searchEngineControllerSEO@ecomEmbeddedRecommendation_v2');
Route::get('/ecomPageHistory', 'searchEngineControllerSEO@ecomPageHistory');
Route::get('/ecomHistory', 'searchEngineControllerSEO@ecomHistory');
Route::get('/seoItemWord', 'searchEngineControllerSEO@seoItemWord');

Route::get('/hottestNewProduct', 'searchEngineControllerSEO@hottestNewProduct');

Route::get('/productEcom','gatherPageApiController@get_product_for_ecom');//電商推薦商品集合頁 滑不完
Route::get('/productMedia','gatherPageApiController@get_product_for_media');//媒體電商化
Route::get('/articleMedia','gatherPageApiController@get_article_for_media');//文章推薦文章
Route::get('/getAd','gatherPageApiController@get_ad');//文章推薦廣告
Route::get('/getOnpage','gatherPageApiController@get_onpage_data');//onpage_data
Route::get('/getMediaOnpage','gatherPageApiController@media_onpage_info');//onpage_data
Route::get('/getOnpage_test','gatherPageApiController@get_onpage_data_test');//onpage_data_test
Route::get('/getMediaOnpage_test','gatherPageApiController@media_onpage_info_test');//onpage_data_test
Route::get('/getuuidpurchased','gatherPageApiController@get_uuid_purchased');//滑不完購買紀錄
Route::get('/getOnpageRe','gatherPageApiController@get_onpage_data_re_test');//onpage購買紀錄test

Route::get('/getSlidingConfig','getSlidingConfigApiController@get_config');//滑不完config api
Route::get('/getTrackingConfig','getTrackingConfigApiController@get_config');//tracking code config api
Route::get('/tracking/enable','getTrackingConfigApiController@check_enable');//tracking code 開關
Route::get('/tracking/config','getTrackingConfigApiController@get_config2');//tracking code config api


Route::get('/coupon/enable','getCouponRelatedApiController@check_enable');//addfan coupon,ad code總開關
Route::get('/coupon/status','getCouponRelatedApiController@get_coupon_status');//coupon status
Route::get('/coupon/model','getCouponRelatedApiController@get_coupon_model');//coupon model
Route::get('/coupon/model2','getCouponRelatedApiController@get_coupon_model2');//coupon model
Route::get('/coupon/details','getCouponRelatedApiController@get_coupon');//coupon
Route::post('/coupon/batchStatus','getCouponRelatedApiController@chage_coupon_is_sent');//批量代碼改變狀態

Route::get('/coupon/ad_status','getCouponRelatedApiController@get_ad_status');//ad status
Route::get('/coupon/ad_details','getCouponRelatedApiController@get_ad');//ad details


Route::get('/getGAEventWebId', 'recommendationSettingsController@getGAEventWebId');//使用GA事件的web id名單
Route::get('/getIgnoreUTMWebId', 'recommendationSettingsController@getIgnoreUTMWebId');//不使用UTM的web id名單

Route::get('/getCoupon','gatherPageApiController@coupon_onpage');
//以下Route指向的function不存在，先關閉
// Route::get('/searchEngine', 'searchEngineController@searchEngine');
// Route::get('/recommendWord', 'searchEngineController@getRecommendWord');
// Route::get('/hotWord', 'searchEngineController@getHotWord');

// Test
Route::get('/test/toggleDatabase', 'recommendationConfigController@toggleRecommendDB');
Route::get('/test/toggleFile', 'recommendationConfigController@toggleRecommendFile');
Route::get('/test/toggleBlockDatabase', 'recommendationConfigController@toggleBlockDB');
Route::get('/test/toggleBlockFile', 'recommendationConfigController@toggleBlockFile');
Route::get('/test/amikoKeyword', 'amikoKeywordController@amikoKeyword');

Route::get('/test/searchEngineCdn', 'searchEngineController_test@searchEngine_cdn');
Route::get('/test/recommendWordCdn', 'searchEngineController_test@getRecommendWordCdn');
Route::get('/test/getHotWordCdn', 'searchEngineController_test@getHotWordCdn');
Route::get('/test/getAlsoWatchedWordCdn', 'searchEngineController_test@getAlsoWatchedWordCdn');
Route::get('/test/getUuidLikeWordCdn', 'searchEngineController_test@getUuidLikeWordCdn');
Route::post('/test/getHotWordCdnOutside', 'searchEngineController_test@getHotWordCdnOutside');
Route::post('/test/searchEngineCdnOutside', 'searchEngineController_test@searchEngine_cdnOutside');
Route::get('/test/ecomEmbeddedRecommendation', 'searchEngineControllerSEO_test@ecomEmbeddedRecommendation_v2');
Route::get('/test/ecomEmbeddedRecommendation_v2', 'searchEngineControllerSEO_test@ecomEmbeddedRecommendation_v2');
Route::get('/test/ecomPageHistory', 'searchEngineControllerSEO_test@ecomPageHistory');
Route::get('/test/ecomHistory', 'searchEngineControllerSEO_test@ecomHistory');
Route::get('/test/seoItemWord', 'searchEngineControllerSEO_test@seoItemWord');
Route::get('/test/hottestNewProduct', 'searchEngineControllerSEO_test@hottestNewProduct');
Route::get('/test/productEcom','gatherPageApiController_test@get_product_for_ecom');//電商推薦商品集合頁 滑不完
Route::get('/test/productMedia','gatherPageApiController_test@get_product_for_media');//媒體電商化
Route::get('/test/articleMedia','gatherPageApiController_test@get_article_for_media');//文章推薦文章
Route::get('/test/getAd','gatherPageApiController_test@get_ad');//文章推薦廣告
Route::get('/test/getOnpage','gatherPageApiController_test@get_onpage_data');//onpage_data
Route::get('/test/getMediaOnpage','gatherPageApiController_test@media_onpage_info');//onpage_data
Route::get('/test/getOnpage_test','gatherPageApiController@get_onpage_data_test');//onpage_data_test
Route::get('/test/getMediaOnpage_test','gatherPageApiController_test@media_onpage_info_test');//onpage_data_test
Route::get('/test/getuuidpurchased','gatherPageApiController_test@get_uuid_purchased');//滑不完購買紀錄
Route::get('/test/getOnpageRe','gatherPageApiController_test@get_onpage_data_re_test');//onpage購買紀錄test