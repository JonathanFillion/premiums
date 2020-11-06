<?php

use Illuminate\Support\Facades\Route;
use App\Models\Price;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

function trimData($data) {
  $find = array("$","CA", ",");
  return str_replace($find, "", $data);;
}

function getStringBetween($string, $start, $end){
  $string = ' ' . $string;
  $ini = strpos($string, $start);
  if ($ini == 0) return '';
  $ini += strlen($start);
  $len = strpos($string, $end, $ini) - $ini;
  return substr($string, $ini, $len);
}

/*

php artisan migrate:refresh --seed

*/

Route::get('/', function () {

  $batch_number = 1;
/*
    $most_recent_batch_number = App\Models\Price::orderBy('batch', 'desc')
        ->first();

    dump($most_recent_batch_number->batch);

    $batch_number = $most_recent_batch_number->batch + 1;

*/
    $crawler = Goutte::request('GET', 'https://www.kitco.com/charts/livesilver.html');
    
    $silver = trimData($crawler->filter('#sp-ask')->text());

    $silver_obj = new Price;
    $silver_obj->price = $silver;
    $silver_obj->type = "silver";
    $silver_obj->batch = $batch_number;
    $silver_obj->save();


    //CANADAGOLD

    $crawler = Goutte::request('GET', 'https://canadagold.ca/what-we-sell/');

    $canadagold_1oz_sm_unparsed = $crawler->filter('.cg-table')->eq(1)->filter('tr')->eq(0)->filter('td')->eq(2)->text();
    $canadagold_1oz_sm = trimData(getStringBetween($canadagold_1oz_sm_unparsed, "Up to 250 w/o DNA*: ", " each250"));

    $canadagold_10oz_bar_unparsed = $crawler->filter('.cg-table')->eq(1)->filter('tr')->eq(4)->filter('td')->eq(2)->text();
    $canadagold_10oz_bar = trimData(getStringBetween($canadagold_10oz_bar_unparsed, "1-24 Bars: ", " each25+"));

    $canadagold_100oz_bar_unparsed = $crawler->filter('.cg-table')->eq(1)->filter('tr')->eq(5)->filter('td')->eq(2)->text();
    $canadagold_100oz_bar = trimData(getStringBetween($canadagold_100oz_bar_unparsed, "1-3 Bars: ", " each4+"));


    //CANADIANPMX
    
    $crawler = Goutte::request('GET', 'https://canadianpmx.com/product/2020-canadian-silver-maple-leaf-1-oz-9999/');
    $canadianpmx_1oz_sm = trimData($crawler->filter('.vpcol02')->text());
    
    $crawler = Goutte::request('GET', 'https://canadianpmx.com/product/misc-silver-bar-10-oz-999/');
    $canadianpmx_10oz_bar = trimData($crawler->filter('.vpcol02')->text());

    $crawler = Goutte::request('GET', 'https://canadianpmx.com/product/silver-bar-100-oz-royal-canadian-mint-9999/');
    $canadianpmx_100oz_bar = trimData($crawler->filter('.vpcol02')->text());


    //CANADIANBULLION

    $crawler = Goutte::request('GET', 'https://canadianbullion.ca/silver/coins/1-oz-2016-canadian-maple-leaf-silver-coin.html');
    $canadianbullion_1oz_sm = trimData($crawler->filter('.regular-price')->filter('.price')->text());

    $crawler = Goutte::request('GET', 'https://canadianbullion.ca/silver/10-oz-silver-bar/10-oz-sprott-silver-bar.html');
    $canadianbullion_10oz_bar = trimData($crawler->filter('.regular-price')->filter('.price')->text());

    $crawler = Goutte::request('GET', 'https://canadianbullion.ca/silver/100-oz-silver-bar/100-oz-sunshine-mint-silver-bar.html');
    $canadianbullion_100oz_bar =trimData($crawler->filter('.regular-price')->filter('.price')->text());


  /*dump($canadagold_1oz_sm);
    dump($canadagold_10oz_bar);
    dump($canadagold_100oz_bar);

    dump($canadianpmx_1oz_sm);
    dump($canadianpmx_10oz_bar);
    dump($canadianpmx_100oz_bar);

    dump($canadianbullion_1oz_sm);
    dump($canadianbullion_10oz_bar);
    dump($canadianbullion_100oz_bar);*/

    return view('welcome');
  });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


















































/*
    //SILVERGOLDBULL
    //Cookie: frontend=8srenkm9kdjldsk9slo3uk02bseogksf; init-dropdown-shown=1; page-announce2-alert=closed
    //https://silvergoldbull.ca/ajax/index/rates
    //POST
        //base_url: https://silvergoldbull.ca/
        //rnd: 0.7579420856678756
        //p: 189
        //b: 
        //g: 1
        //c: CAD
        //cs: $
        //s: 1
        //w: 1
        //co: CA


    $crawler = Goutte::request('GET', 'https://silvergoldbull.ca/1oz-pure-canadian-silver-maple-leaf-coin');
    sleep(10);
    $silvergoldbull_1oz_sm = $crawler->filter('.tiers')->filter('td');
    dump($silvergoldbull_1oz_sm);

    $crawler = Goutte::request('GET', 'https://silvergoldbull.ca/1oz-pure-canadian-silver-maple-leaf-coin');
    //$silvergoldbull_5oz_bar

    $crawler = Goutte::request('GET', 'https://silvergoldbull.ca/10oz-pure-silver-bar');
    //$silvergoldbull_10oz_bar
    */



/*    //JMBULLION (Out of stock & !discount)

    $jmbullion_1oz_ae = ""; $jmbullion_5oz_bar = ""; $jmbullion_10oz_bar = "";
    $jmbullion_1oz_ae_oos = false; $jmbullion_5oz_bar_oos = false; $jmbullion_10oz_bar_oos = false;
    
    $crawler = Goutte::request('GET', 'https://www.jmbullion.com/american-silver-eagle-varied-year/');
    if(!$crawler->filter('.outofstock')->count()){
        $jmbullion_1oz_ae = trimData($crawler->filter('.payment-section')->filter('table')->filter('tr')->filter('td')->eq(5)->text());

        $jmbullion_1oz_ae_obj = new Price;
        $jmbullion_1oz_ae_obj->price = $jmbullion_1oz_ae;
        $jmbullion_1oz_ae_obj->type = "jmbullion_1oz_ae";
        $jmbullion_1oz_ae_obj->batch = $batch_number;
        $jmbullion_1oz_ae_obj->save();

    } else {
        $jmbullion_1oz_ae_oos = true;
    }
    
    $crawler = Goutte::request('GET', 'https://www.jmbullion.com/5-oz-silvertowne-eagle-silver-bar-new/');
    if(!$crawler->filter('.outofstock')->count()){
        $jmbullion_5oz_bar = trimData($crawler->filter('.payment-section')->filter('table')->filter('tr')->filter('td')->eq(5)->text());

        $jmbullion_5oz_bar_obj = new Price;
        $jmbullion_5oz_bar_obj->price = $jmbullion_5oz_bar;
        $jmbullion_5oz_bar_obj->type = "jmbullion_5oz_bar";
        $jmbullion_5oz_bar_obj->batch = $batch_number;
        $jmbullion_5oz_bar_obj->save();

    } else {
        $jmbullion_5oz_bar_oos = true;
    }
    
    $crawler = Goutte::request('GET', 'https://www.jmbullion.com/10-oz-silver-bar/');
    if(!$crawler->filter('.outofstock')->count()){
        $jmbullion_10oz_bar = trimData($crawler->filter('.payment-section')->filter('table')->filter('tr')->filter('td')->eq(5)->text());

        $jmbullion_10oz_bar_obj = new Price;
        $jmbullion_10oz_bar_obj->price = $jmbullion_10oz_bar;
        $jmbullion_10oz_bar_obj->type = "jmbullion_10oz_bar";
        $jmbullion_10oz_bar_obj->batch = $batch_number;
        $jmbullion_10oz_bar_obj->save();

    } else {
        $jmbullion_10oz_bar_oos = true;
    }
    

    //==================================

    //KITCO (Discount & out of stock)

    $kitco_1oz_ae = ""; $kitco_5oz_bar = ""; $kitco_10oz_bar = "";
    $kitco_1oz_ae_oos = false; $kitco_5oz_bar_oos = false; $kitco_10oz_bar_oos = false;

    $crawler = Goutte::request('GET', 'https://online.kitco.com/buy/3004/1-oz-Silver-American-Eagle-Coin-999-3004');
    if(strcmp($crawler->filter('.msg_notice')->text(), "") == 0 ){
        $kitco_1oz_ae = trimData($crawler->filter('.bulk_discount_list')->filter('td')->eq(1)->text());

        $kitco_1oz_ae_obj = new Price;
        $kitco_1oz_ae_obj->price = $kitco_1oz_ae;
        $kitco_1oz_ae_obj->type = "kitco_1oz_ae";
        $kitco_1oz_ae_obj->batch = $batch_number;
        $kitco_1oz_ae_obj->save();
    }

    $crawler = Goutte::request('GET', 'https://online.kitco.com/buy/1002/5-oz-Silver-Bar-999-1002');
    if(strcmp($crawler->filter('.msg_notice')->text(), "") == 0 ){
        $kitco_5oz_bar = trimData($crawler->filter('.bulk_discount_list')->filter('td')->eq(1)->text());

        $kitco_5oz_bar_obj = new Price;
        $kitco_5oz_bar_obj->price = $kitco_5oz_bar;
        $kitco_5oz_bar_obj->type = "kitco_5oz_bar";
        $kitco_5oz_bar_obj->batch = $batch_number;
        $kitco_5oz_bar_obj->save();
    }
    
    $crawler = Goutte::request('GET', 'https://online.kitco.com/buy/1003/10-oz-Silver-Bar-999-1003');
    if(strcmp($crawler->filter('.msg_notice')->text(), "") == 0 ){
        $kitco_10oz_bar = trimData($crawler->filter('.bulk_discount_list')->filter('td')->eq(1)->text());

        $kitco_10oz_bar_obj = new Price;
        $kitco_10oz_bar_obj->price = $kitco_10oz_bar;
        $kitco_10oz_bar_obj->type = "kitco_10oz_bar";
        $kitco_10oz_bar_obj->batch = $batch_number;
        $kitco_10oz_bar_obj->save();
    }

    //==================================

    //APMEX (Discount & out of stock)

    $apmex_1oz_ae = ""; $apmex_5oz_bar = ""; $apmex_10oz_bar = "";
    $apmex_1oz_ae_oos = false; $apmex_5oz_bar_oos = false; $apmex_10oz_bar_oos = false; 

    $crawler = Goutte::request('GET', 'https://www.apmex.com/product/23331/1-oz-american-silver-eagle-bu-random-year');
    $apmex_1oz_ae_node = $crawler->filter('.item-right')->filter('.price');
    
    if($apmex_1oz_ae_node->eq(0)->children()->count() == 2){
        $apmex_1oz_ae = trimData($apmex_1oz_ae_node->filter('.lowestprice')->text());
    } else {
        $apmex_1oz_ae = trimData($apmex_1oz_ae_node->text());
    }

    $apmex_1oz_ae_obj = new Price;
    $apmex_1oz_ae_obj->price = $apmex_1oz_ae;
    $apmex_1oz_ae_obj->type = "apmex_1oz_ae";
    $apmex_1oz_ae_obj->batch = $batch_number;
    $apmex_1oz_ae_obj->save();

    $crawler = Goutte::request('GET', 'https://www.apmex.com/product/10449/5-oz-silver-bar-secondary-market');
    $apmex_5oz_bar_node = $crawler->filter('.item-right')->filter('.price');
    if($apmex_5oz_bar_node->eq(0)->children()->count() == 2) {
        $apmex_5oz_bar = trimData($apmex_5oz_bar_node->filter('.lowestprice')->text());
    } else {
        $apmex_5oz_bar = trimData($apmex_5oz_bar_node->text());
    }

    $apmex_5oz_bar_obj = new Price;
    $apmex_5oz_bar_obj->price = $apmex_5oz_bar;
    $apmex_5oz_bar_obj->type = "apmex_5oz_bar";
    $apmex_5oz_bar_obj->batch = $batch_number;
    $apmex_5oz_bar_obj->save();

    $crawler = Goutte::request('GET', 'https://www.apmex.com/product/21/10-oz-silver-bar-secondary-market');
    $apmex_10oz_bar_node = $crawler->filter('.item-right')->filter('.price');
    if($apmex_10oz_bar_node->eq(0)->children()->count() == 2) {
        $apmex_10oz_bar = trimData($apmex_10oz_bar_node->filter('.lowestprice')->text());
    } else {
        $apmex_10oz_bar = trimData($apmex_10oz_bar_node->text());
    }

    $apmex_10oz_bar_obj = new Price;
    $apmex_10oz_bar_obj->price = $apmex_10oz_bar;
    $apmex_10oz_bar_obj->type = "apmex_10oz_bar";
    $apmex_10oz_bar_obj->batch = $batch_number;
    $apmex_10oz_bar_obj->save();

    //==================================

    //MONEYMETALS (!Discount & !out of stock)

    $crawler = Goutte::request('GET', 'https://www.moneymetals.com/american-silver-eagle-random-year/262');
    $moneymetals_1oz_ae = trimData($crawler->filter('#premium_data_parent')->filter('tr')->eq(0)->filter('td')->eq(2)->text());

    $moneymetals_1oz_ae_obj = new Price;
    $moneymetals_1oz_ae_obj->price = $moneymetals_1oz_ae;
    $moneymetals_1oz_ae_obj->type = "moneymetals_1oz_ae";
    $moneymetals_1oz_ae_obj->batch = $batch_number;
    $moneymetals_1oz_ae_obj->save();


    $crawler = Goutte::request('GET', 'https://www.moneymetals.com/5-oz-silver-bar/164');
    $moneymetals_5oz_bar = trimData($crawler->filter('#premium_data_parent')->filter('tr')->eq(0)->filter('td')->eq(2)->text());

    $moneymetals_5oz_bar_obj = new Price;
    $moneymetals_5oz_bar_obj->price = $moneymetals_5oz_bar;
    $moneymetals_5oz_bar_obj->type = "moneymetals_5oz_bar";
    $moneymetals_5oz_bar_obj->batch = $batch_number;
    $moneymetals_5oz_bar_obj->save();

    $crawler = Goutte::request('GET', 'https://www.moneymetals.com/10-oz-silver-bars/37');
    $moneymetals_10oz_bar = trimData($crawler->filter('#premium_data_parent')->filter('tr')->eq(0)->filter('td')->eq(2)->text());

    $moneymetals_10oz_bar_obj = new Price;
    $moneymetals_10oz_bar_obj->price = $moneymetals_10oz_bar;
    $moneymetals_10oz_bar_obj->type = "moneymetals_10oz_bar";
    $moneymetals_10oz_bar_obj->batch = $batch_number;
    $moneymetals_10oz_bar_obj->save();


    //==================================
*/