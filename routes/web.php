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


Route::group([ 'prefix' => 'android-api'], function() {
    Route::get('/', 'AndroidApiController@index');
});

Route::get('thetvdbshows', 'ShowsController@thetvdbshows');
Route::get('import', 'ShowsController@import');
Route::get('import2', 'ShowsController@import2');
Route::get('api', 'ShowsController@api');
Route::get('images', 'ShowsController@images');

Route::get('/', 'ShowsController@index', function ($lang) {});
Route::get('actors', 'ActorsController@index', function ($slug) {});
Route::get('{lang}/shows/{slug}', 'ShowsController@detailTranslate', function ($lang, $slug) {});
Route::get('/{lang}', 'ShowsController@index', function ($lang) {});
Route::get('{lang}/actors', 'ActorsController@index', function ($lang) {});
Route::get('{lang}/actors/{slug}', 'ActorsController@detailTranslation', function ($lang, $slug) {});
Route::get('{lang}/networks/{slug}', 'NetworksController@detailTranslate', function ($lang, $slug) {});
Route::get('{lang}/genres/{slug}', 'TermsController@detailTranslate', function ($lang, $slug) {});
Route::get('actors/{slug}', 'ActorsController@detail', function ($slug) {});
Route::get('shows/{slug}', 'ShowsController@detail', function ($slug) {});
Route::get('genres/{slug}', 'TermsController@detail', function ($slug) {});
Route::get('networks/{slug}', 'NetworksController@detail', function ($slug) {});