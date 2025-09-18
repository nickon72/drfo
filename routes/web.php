<?php

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


Route::get('/', 'HomeController@index');


Route::group(['middleware' => 'manager'], function() {
    Route::get('/region', 'RegionController@index');

    Route::get('/drfo', 'DrfoController@index');
    Route::post('/drfo_date', 'DrfoController@index_date')->name('drfo_date');;

    Route::get('/residence_filia', 'ResidenceController@residence_filia')->name('residence_filia');
    Route::post('/residence_filia_show', 'ResidenceController@residence_filia_show')->name('residence_filia_show');

    Route::get('/residence_people_show/{drfo}', 'ResidenceController@show')->name('residence_people_show');

    Route::get('/gorod_kirovograd', 'ResidenceController@kirovograd')->name('gorod_kirovograd');
    Route::get('/gorod_any', 'ResidenceController@any')->name('gorod_any');
    Route::get('/gorod_any_region/{region_id}', 'ResidenceController@any_region')->name('gorod_any_region');
    Route::get('/gorod_any_settlement/{region_id}/{residence_locality}', 'ResidenceController@any_settlement')->name('gorod_any_settlement');


    Route::get('/gorod_kirovograd_street/{residence_street}', 'ResidenceController@kirovograd_street')->name('gorod_kirovograd_street');
    Route::get('/gorod_settlement_street/{region_id}/{residence_locality}/{residence_street}', 'ResidenceController@settlement_street')->name('gorod_settlement_street');

    Route::get('/gorod_kirovograd_street_house/{residence_street}/{house_number}/{house_letter?}', 'ResidenceController@kirovograd_street_house')->name('gorod_kirovograd_street_house');

    Route::get('/gorod_settlement_street_house/{region_id}/{residence_locality}/{residence_street}/{house_number}/{house_letter?}', 'ResidenceController@settlement_street_house')->name('gorod_settlement_street_house');

    // экспорт в ексель три роута по городу, улице, дому
    Route::get('/export-residence/{region_id}/{residence_locality}', 'ResidenceController@exportResidenceDataGorod')->name('export_residence_data_gorod');
    Route::get('/export-residence/{region_id}/{residence_locality}/{residence_street}', 'ResidenceController@exportResidenceData')->name('export_residence_data');
    Route::get('/export-residence/{region_id}/{residence_locality}/{residence_street}/{house_number}', 'ResidenceController@exportResidenceDataHouse')->name('export_residence_data_house');


// для экспорта в ексель таблиц по кировораду и сваляве не будет использоваться напрямую
    Route::get('/export-residence_kirovograd/{region_id}/{residence_locality}/{residence_street}', 'ResidenceController@exportResidenceKirovograd')->name('export_residence_kirovograd');
    Route::get('/export-residence_svalyava/{region_id}/{residence_locality}/{residence_street}', 'ResidenceController@exportResidenceSvalyava')->name('export_residence_svalyava');

    // блок роутов по Кировограду используется таблица Kirovograd
    Route::get('/kirovograd', 'KirovogradController@kirovograd')->name('kirovograd');
    Route::get('/kirovograd_kirovograd', 'KirovogradController@residence_kirovograd')->name('kirovograd_kirovograd');
    Route::post('/kirovograd_kirovograd_show', 'KirovogradController@residence_kirovograd_show')->name('kirovograd_kirovograd_show');
    Route::get('/kirovograd_any_settlement', 'KirovogradController@any_settlement')->name('kirovograd_any_settlement');
    Route::get('/kirovograd_settlement_street/{residence_street}', 'KirovogradController@settlement_street')->name('kirovograd_settlement_street');
    Route::get('/kirovograd_kirovograd_street_house/{residence_street}/{house_number}/{house_letter?}', 'KirovogradController@kirovograd_street_house')->name('kirovograd_kirovograd_street_house');

    Route::get('/export-kirovograd/', 'KirovogradController@exportKirovograd')->name('export_kirovograd');
    Route::get('/export-residence_data_kirovograd/{residence_street}/{house_number?}', 'KirovogradController@exportResidenceKirovograd')->name('export_residence_data_kirovograd');
    Route::get('/export-residence_kirovograd_house/{residence_street}/{house_number?}', 'KirovogradController@exportResidenceKirovograd')->name('export_residence_kirovograd_house');
    //Route::get('/export-residence_svalyava/{region_id}/{residence_locality}/{residence_street}/{house_number?}', [ResidenceController::class, 'exportResidenceSvalyava'])->name('export_residence_svalyava');


    // блок роутов по Свалява используется таблица svalyava
    Route::get('/svalyava', 'SvalyavaController@kirovograd')->name('svalyava');
    Route::get('/svalyava_svalyava', 'SvalyavaController@residence_svalyava')->name('svalyava_svalyava');
    Route::post('/svalyava_svalyava_show', 'SvalyavaController@residence_svalyava_show')->name('svalyava_svalyava_show');
    Route::get('/svalyava_any_settlement', 'SvalyavaController@any_settlement')->name('svalyava_any_settlement');
    Route::get('/svalyava_settlement_street/{residence_street}', 'SvalyavaController@settlement_street')->name('svalyava_settlement_street');
    Route::get('/svalyava_svalyava_street_house/{residence_street}/{house_number}/{house_letter?}', 'SvalyavaController@svalyava_street_house')->name('svalyava_svalyava_street_house');

    Route::get('/export-svalyava/', 'SvalyavaController@exportSvalyava')->name('export_svalyava');
    Route::get('/export-residence_data_svalyava/{residence_street}/{house_number?}', 'SvalyavaController@exportResidenceSvalyava')->name('export_residence_data_svalyava');
    Route::get('/export-residence_svalyava_house/{residence_street}/{house_number?}', 'SvalyavaController@exportResidenceSvalyava')->name('export_residence_svalyava_house');



});


Route::group(['middleware' => 'guest'], function() {
    Route::get('/register', 'AuthController@registerForm');
    Route::post('/register', 'AuthController@register');
    Route::get('/login', 'AuthController@loginForm')->name('login');
    Route::post('/login', 'AuthController@login');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('/profile','ProfileController@index');
    Route::post('/profile','ProfileController@store');
    Route::get('logout', 'AuthController@logout');
});


Route::group(['prefix' =>'admin', 'namespace' => 'Admin','middleware' => 'admin'], function(){

    Route::get('/', 'DashboardController@index');


    Route::resource('/users','UsersController');
    Route::get('/users/toggle/{id}','UsersController@toggle');


});