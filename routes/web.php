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



Route::group(['prefix' => 'android-api'], function() {
    Route::get('/', 'AndroidApiController@index');
});

Route::get('thetvdbshows', 'ShowsController@thetvdbshows');

Route::get('import/{thetvdbId?}', 'ShowsController@import', function ($thetvdbId) {
    
});


Route::group(['prefix' => 'cron'], function() {
    Route::get('import', 'CronController@import'); //-> call the server.cz/import
});


Route::get('api', 'ShowsController@api');
Route::get('images', 'ShowsController@images');

Route::post('search', 'ShowsController@search');
Route::get('search', 'ShowsController@search');

Route::get('update-shows-slugs', 'ShowsController@updateSlugs');

Route::get('/', 'ShowsController@index', function ($lang) {
    
});


Route::get('actors', 'ActorsController@index');

Route::get('calendar', 'PagesController@calendar');



Route::group(['prefix' => '{lang}'], function() {

    Auth::routes();

    Route::get('', 'ShowsController@index', function ($lang) {
        
    });

    Route::get('shows/{slug}', 'ShowsController@detail', function ($lang, $slug) {
        
    });
    Route::get('shows/{slugShow}/{slugEpisode}', 'EpisodesController@detail', function ($lang, $slugShow, $slugEpisode) {
        
    });

    Route::get('actors', 'ActorsController@index', function ($lang) {
        
    });
    Route::get('actors/{slug}', 'ActorsController@detail', function ($lang, $slug) {
        
    });
    Route::get('networks/{slug}', 'NetworksController@detail', function ($lang, $slug) {
        
    });
    Route::get('genres/{slug}', 'TermsController@detail', function ($lang, $slug) {
        
    });
});

Route::get('actors/{slug}', 'ActorsController@detail', function ($slug) {
    
});
Route::get('shows/{slug}', 'ShowsController@detail', function ($slug) {
    
});

Route::get('shows/{slugShow}/{slugEpisode}', 'EpisodesController@detail', function ($slugShow, $slugEpisode) {
    
});

Route::get('genres/{slug}', 'TermsController@detail', function ($slug) {
    
});
Route::get('networks/{slug}', 'NetworksController@detail', function ($slug) {
    
});

Route::resource('comments', 'CommentsController');
Route::resource('users', 'UsersController');


Auth::routes();

Route::group(['prefix' => 'sitemap'], function() {
    Route::get('full', 'SitemapController@full');
    Route::get('shows', 'SitemapController@shows');
    Route::get('episodes', 'SitemapController@episodes');
    Route::get('actors', 'SitemapController@actors');
    Route::get('networks', 'SitemapController@networks');
    Route::get('genres', 'SitemapController@genres');
    Route::get('sitemapindex', 'SitemapController@sitemapindex');
    
    Route::group(['prefix' => 'cron'], function() {
        Route::get('generate', 'SitemapController@generate');
    });
});

