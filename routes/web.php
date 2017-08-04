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

Route::get('/', 'ShowsController@index', function ($lang) {
    
});


Route::get('actors', 'ActorsController@index', function ($slug) {
    
});

Route::group(['prefix' => '{lang}'], function() {

    Auth::routes();

    Route::get('', 'ShowsController@index', function ($lang) {
        
    });

    Route::get('shows/{slug}', 'ShowsController@detailTranslate', function ($lang, $slug) {
        
    });
    Route::get('shows/{slugShow}/{slugEpisode}', 'EpisodesController@detailTranslate', function ($lang, $slugShow, $slugEpisode) {
        
    });

    Route::get('actors', 'ActorsController@index', function ($lang) {
        
    });
    Route::get('actors/{slug}', 'ActorsController@detailTranslation', function ($lang, $slug) {
        
    });
    Route::get('networks/{slug}', 'NetworksController@detailTranslate', function ($lang, $slug) {
        
    });
    Route::get('genres/{slug}', 'TermsController@detailTranslate', function ($lang, $slug) {
        
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
/*
Route::group(['middleware' => 'auth'], function() {
    Route::resource('comments', 'CommentsController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
});
*/

Auth::routes();

Route::get('sitemap', function() {

    // create new sitemap object
    $sitemap = App::make("sitemap");

    // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
    // by default cache is disabled
    //$sitemap->setCache('laravel.sitemap', 60);
    // check if there is cached sitemap and build new only if is not
    if (/* !$sitemap->isCached() || */true) {
        // add item to the sitemap (url, date, priority, freq)
        //$sitemap->add(URL::to('/'), '2012-08-25T20:10:00+02:00', '1.0', 'daily');

        /*
          $sitemap->add(URL::to('page'), '2012-08-26T12:30:00+02:00', '0.9', 'monthly');

          // add item with translations (url, date, priority, freq, images, title, translations)
          $translations = [
          ['language' => 'fr', 'url' => URL::to('pageFr')],
          ['language' => 'de', 'url' => URL::to('pageDe')],
          ['language' => 'bg', 'url' => URL::to('pageBg')],
          ];

          $sitemap->add(URL::to('pageEn'), '2015-06-24T14:30:00+02:00', '0.9', 'monthly', [], null, $translations);

          // add item with images
          $images = [
          ['url' => URL::to('images/pic1.jpg'), 'title' => 'Image title', 'caption' => 'Image caption', 'geo_location' => 'Plovdiv, Bulgaria'],
          ['url' => URL::to('images/pic2.jpg'), 'title' => 'Image title2', 'caption' => 'Image caption2'],
          ['url' => URL::to('images/pic3.jpg'), 'title' => 'Image title3'],
          ];

          $sitemap->add(URL::to('post-with-images'), '2015-06-24T14:30:00+02:00', '0.9', 'monthly', $images);
         */

        // get all posts from db
        $shows = App\Show::orderBy('updated_at', 'desc')->get();

        // add every post to the sitemap
        foreach ($shows as $show) {
            $sitemap->add($show->url(), $show->updated_at, $show->rating, 'monthly');
        }
    }

    // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
    return $sitemap->render('xml');
});
