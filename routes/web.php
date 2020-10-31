<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function() {
    /*dump($crawler);*/

    /*sp-ask*/

    /*$crawler->filter('.result__title .result__a')->each(function ($node) {
      dump($node->text());
    });

    */
    $crawler = Goutte::request('GET', 'https://www.kitco.com/charts/livesilver.html');
    $silver_price = $crawler->filter('#sp-ask')->text();

    //dump($silver_price);

    //JMBULLION (Out of stock & !discount)

    $jmbullion_1oz_ae = ""; $jmbullion_5oz_bar = ""; $jmbullion_10oz_bar = "";
    $jmbullion_1oz_ae_oos = false; $jmbullion_5oz_bar_oos = false; $jmbullion_10oz_bar_oos = false;
    
    $crawler = Goutte::request('GET', 'https://www.jmbullion.com/american-silver-eagle-varied-year/');
    if($crawler->filter('.outofstock')->count()){
        $jmbullion_1oz_ae_oos = true;
    }
    $jmbullion_1oz_ae = $crawler->filter('.payment-section')->filter('table')->filter('tr')->filter('td')->eq(5)->text();
    
    $crawler = Goutte::request('GET', 'https://www.jmbullion.com/5-oz-silvertowne-eagle-silver-bar-new/');
    if($crawler->filter('.outofstock')->count()){
        $jmbullion_5oz_bar_oos = true;
    }
    $jmbullion_5oz_bar = $crawler->filter('.payment-section')->filter('table')->filter('tr')->filter('td')->eq(5)->text();
    
    $crawler = Goutte::request('GET', 'https://www.jmbullion.com/10-oz-silver-bar/');
    if($crawler->filter('.outofstock')->count()){
        $jmbullion_10oz_bar_oos = true;
    }
    $jmbullion_10oz_bar = $crawler->filter('.payment-section')->filter('table')->filter('tr')->filter('td')->eq(5)->text();
    

    //==================================

    //KITCO (Discount & out of stock)

	$kitco_1oz_ae = ""; $kitco_5oz_bar = ""; $kitco_10oz_bar = "";
    $kitco_1oz_ae_oos = false; $kitco_5oz_bar_oos = false; $kitco_10oz_bar_oos = false;

    $crawler = Goutte::request('GET', 'https://online.kitco.com/buy/3004/1-oz-Silver-American-Eagle-Coin-999-3004');
    if(strcmp($crawler->filter('.msg_notice')->text(), "") == 0 ){
        $kitco_1oz_ae = $crawler->filter('.bulk_discount_list')->filter('td')->eq(1)->text();
    }

    $crawler = Goutte::request('GET', 'https://online.kitco.com/buy/1002/5-oz-Silver-Bar-999-1002');
    if(strcmp($crawler->filter('.msg_notice')->text(), "") == 0 ){
        $kitco_5oz_bar = $crawler->filter('.bulk_discount_list')->filter('td')->eq(1)->text();
    }
    
    $crawler = Goutte::request('GET', 'https://online.kitco.com/buy/1003/10-oz-Silver-Bar-999-1003');
    if(strcmp($crawler->filter('.msg_notice')->text(), "") == 0 ){
        $kitco_10oz_bar = $crawler->filter('.bulk_discount_list')->filter('td')->eq(1)->text();    
    }

    //==================================

    //APMEX (Discount & out of stock)

    $apmex_1oz_ae = ""; $apmex_5oz_bar = ""; $apmex_10oz_bar = "";
    $apmex_1oz_ae_oos = false; $apmex_5oz_bar_oos = false; $apmex_10oz_bar_oos = false; 

    $crawler = Goutte::request('GET', 'https://www.apmex.com/product/23331/1-oz-american-silver-eagle-bu-random-year');
    $apmex_1oz_ae_node = $crawler->filter('.item-right')->filter('.price');
    
    if($apmex_1oz_ae_node->eq(0)->children()->count() == 2){
        $apmex_1oz_ae = $apmex_1oz_ae_node->filter('.lowestprice')->text();
    } else {
        $apmex_1oz_ae = $apmex_1oz_ae_node->text();
    }

    $crawler = Goutte::request('GET', 'https://www.apmex.com/product/10449/5-oz-silver-bar-secondary-market');
    $apmex_5oz_bar_node = $crawler->filter('.item-right')->filter('.price');
    if($apmex_5oz_bar_node->eq(0)->children()->count() == 2) {
        $apmex_5oz_bar = $apmex_5oz_bar_node->filter('.lowestprice')->text();
    } else {
        $apmex_5oz_bar = $apmex_5oz_bar_node->text();
    }

    $crawler = Goutte::request('GET', 'https://www.apmex.com/product/21/10-oz-silver-bar-secondary-market');
    $apmex_10oz_bar_node = $crawler->filter('.item-right')->filter('.price');
    if($apmex_10oz_bar_node->eq(0)->children()->count() == 2) {
        $apmex_10oz_bar = $apmex_10oz_bar_node->filter('.lowestprice')->text();
    } else {
        $apmex_10oz_bar = $apmex_10oz_bar_node->text();
    }
    //==================================

    //MONEYMETALS (!Discount & !out of stock)

    $crawler = Goutte::request('GET', 'https://www.moneymetals.com/american-silver-eagle-random-year/262');
    $moneymetals_1oz_ae = $crawler->filter('#premium_data_parent')->filter('tr')->eq(0)->filter('td')->eq(2)->text();

    $crawler = Goutte::request('GET', 'https://www.moneymetals.com/5-oz-silver-bar/164');
    $moneymetals_5oz_bar = $crawler->filter('#premium_data_parent')->filter('tr')->eq(0)->filter('td')->eq(2)->text();

    $crawler = Goutte::request('GET', 'https://www.moneymetals.com/10-oz-silver-bars/37');
    $moneymetals_10oz_bar = $crawler->filter('#premium_data_parent')->filter('tr')->eq(0)->filter('td')->eq(2)->text();


    //==================================


    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
