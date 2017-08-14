<?php

namespace App\Http\Controllers;

use App\File;
use App\Show;
use App\Select;
use DB;
use App\Functions\Utils;
use View;
use Request;
use Cocur\Slugify\Slugify;
use App\UserPivot;

define('THETVDB_API_KEY', '5EA3012696C40059');

class ShowsController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function index($lang = DEF_LANG) {


        //Find folfder
        $filter = [
            'genres' => \App\Term::all(),
            'genres_counter' => DB::table('terms_to_models')->select(DB::raw('term_id, count(*) as count'))->groupBy('term_id')->get()->keyBy('term_id')->toArray(),
            'networks' => \App\Option::where('select_id', '=', Select::where('title', '=', 'network')->first()->id)->get(),
            'options_counter' => DB::table('options_to_models')->select(DB::raw('option_id, count(*) as count'))->groupBy('option_id')->get()->keyBy('option_id')->toArray(),
            //'statuses' => \App\Option::where('select_id', '=', Select::where('title', '=', 'status')->first()->id)->get(),
            'runtime' => [
                'min' => Show::min('runtime'),
                'max' => Show::max('runtime'),
            ],
            'orders' => [
                'rating' => 'Hodnocení',
                'created_at' => 'Data přidání',
            ],
        ];

        $limit = 2 * 3 * 5;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $next_page = $page + 1;

        $shows = new Show;
        
        //Terms
        if (isset($_GET['genre'])) {
            $shows = $shows->join('terms_to_models as ttm', 'ttm.model_id', '=', 'shows.id');
            $shows = $shows->whereIn('ttm.term_id', $_GET['genre']);
        }
        //Options
        /*
        $optionsKeys = ['network'];
        $options = [];
        foreach ($optionsKeys as $key) {
            if (isset($_GET[$key])) {
                $shows = $shows->join("options_to_models as $key", "$key.model_id", "=", "shows.id");
                $options = [];
                foreach ($_GET[$key] as $value) {
                    $options[] = $value;
                }
                $shows = $shows->whereIn("$key.option_id", $options);
            }
        }
        */

        //Range
        if (isset($_GET['sMin']) && isset($_GET['sMax'])) {
            $shows = $shows->whereBetween('runtime', [$_GET['sMin'], $_GET['sMax']]);
        }

        $shows = $shows->select(
                DB::raw('(
                        (SELECT count(*) FROM files WHERE model_id = shows.id AND type = "fanart" )/20 +
                        (CASE WHEN translation.content IS NOT NULL THEN 3 ELSE 0 END) + 
                        (CASE WHEN translation.title IS NOT NULL THEN 2 ELSE 0 END) + 
                        (CASE WHEN ended = 0 THEN 6 ELSE 0 END)
                        ) as weight, 
                        shows.*'))
                ->distinct('shows.id');


        $results = $shows->count('shows.id'); //'shows.id'
        //Order
        if (isset($_GET['order']) && !empty($_GET['order'])) {
            $shows = $shows->orderBy($_GET['order'], 'DESC');
        } else {
            
            $shows = $shows->join('shows_translations as translation', 'translation.show_id', '=', 'shows.id');
            $shows = $shows->where('lang', $lang);

            $shows->orderBy('weight', 'desc');
        }
        //$shows = $shows->toSql();
        //dd($shows);

        $shows = $shows->paginate($limit);
        
        
        $more = ceil($results / $limit) > $page ? true : false;

        //Filter
        if (Utils::isAjax()) {

            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");

            if (isset($_GET['query'])) {
                print $this->search($_GET['query'], $lang);
                exit();
            } else {
                $view = View::make('shows.ajax.items', compact(['shows', 'lang', 'page', 'next_page', 'more', 'results']))->render();
                $snippets = [isset($_GET['page']) && $_GET['page'] == 1 ? 'snippet-wrapper' : 'snippet-more' => $view];
                print json_encode(['snippets' => $snippets]);
                exit();
            }
        }

        return view('shows.index', compact(['filter', 'shows', 'lang', 'page', 'next_page', 'more', 'results']));
    }

    public function detail($first = null, $second = null) {

        $lang = null;
        $slug = null;
        if (!empty($second)) {
            $lang = $first;
            $slug = $second;
        } else {
            $lang = DEF_LANG;
            $slug = $first;
        }
        
        
        if (!is_numeric($slug)) {
            $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                    ->where('slug', '=', $slug)
                    ->where('lang', '=', $lang)
                    ->select('shows.*')// just to avoid fetching anything from joined table
                    ->first();

            if (!$show) {
                $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                        ->where('slug', '=', $slug)
                        ->select('shows.*')
                        ->firstOrFail();
            }
        } else {
            $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                    ->where('shows.id', '=', $slug)
                    ->where('lang', '=', $lang)
                    ->select('shows.*')// just to avoid fetching anything from joined table
                    ->first();

            if (!$show) {
                $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                        ->where('shows.id', '=', $slug)
                        ->select('shows.*')
                        ->firstOrFail();
            }
        }

        if (isset($_GET['update']) && $_GET['update'] == 'true') {
            $c = new ShowsController;
            $c->updateShow($show->thetvdb_id);
        }


        //Users actions
        $isFavourite = false;
        if (\Auth::user()) {
            $arr = [
                'user_id' => \Auth::user()->id,
                'model_id' => $show->id,
                'model_type' => $show->type,
                'action' => 'follow',
            ];
            $isFavourite = UserPivot::where($arr)->first() ? true : false;
        }


        $season = null;
        $seasonNum = null;


        if (Utils::isAjax()) {

            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");

            if (isset($_GET['season']) && $_GET['season'] >= 1) {
                $season = $show->seasonEpisodes($_GET['season']);
                $view = View::make('shows.ajax.season', compact(['season', 'lang']))->render();
                $snippets = ['snippet-season-' . $_GET['season'] => $view];
                print json_encode(['snippets' => $snippets]);
                exit();
            }

            //Actions 
            if (isset($_GET['favourite']) && $_GET['favourite'] == true) {

                if (\Auth::user()) {
                    $arr = [
                        'user_id' => \Auth::user()->id,
                        'model_id' => $show->id,
                        'model_type' => $show->type,
                        'action' => 'follow',
                    ];
                    $isFavourite = false;
                    $pivot = UserPivot::where($arr)->first();
                    if ($pivot) {
                        $pivot->delete();
                        $isFavourite = false;
                    } else {
                        UserPivot::create($arr);
                        $isFavourite = true;
                    }

                    $view = View::make('shows.ajax.favourite', compact(['isFavourite']))->render();
                    $snippets = ['snippet-favourite' => $view];
                    print json_encode(['snippets' => $snippets]);
                    exit();
                }

                exit();
            }
        } else if (isset($_GET['season']) && $_GET['season'] >= 1) {
            $season = $show->seasonEpisodes($_GET['season']);
            $seasonNum = $_GET['season'];
        }


        $gallery = $show->files()->where('type', 'fanart')->orderBy('sort', 'desc')->get();
        $fanart = $show->files()->where('type', 'fanart')->orderBy('sort', 'desc')->first();

        return view('shows.detail', compact(['show', 'season', 'seasonNum', 'gallery', 'fanart', 'isFavourite']));
    }

    public function thetvdbshows() {
        ini_set('max_execution_time', 60 * 60 * 5);
        $xml = simplexml_load_string(file_get_contents("public/xml/shows.xml"));
        $shows = $xml->show;
        $statAt = 0; //+1
        $count = 0;
        echo "<pre>";
        foreach ($shows as $theTvDbId) {
            if ($count >= $statAt) {
                $show = simplexml_load_string(file_get_contents("http://thetvdb.com/api/" . THETVDB_API_KEY . "/series/$theTvDbId/en.xml"));
                $item = [
                    'title' => trim($show->Series->SeriesName),
                    'thetvdb_id' => trim($show->Series->id),
                    'rating_count' => trim($show->Series->RatingCount),
                    'fanart' => trim($show->Series->fanart) ? true : false,
                    'poster' => trim($show->Series->poster) ? true : false,
                ];
                DB::table('thetvdbshows')->insert($item);
            }
            $count++;
        }
    }

    //New Api
    public function import($thetvdbId = null) {
        $start = microtime(true);
        ini_set('max_execution_time', 60 * 60 * 10);
        $shows = null;
        if ($thetvdbId == null) {
            $showsInDb = DB::table('shows')->select('thetvdb_id')->get()->toArray();
            $arr = [];
            foreach ($showsInDb as $s) {
                $arr[] = $s->thetvdb_id;
            }

            $shows = DB::table('thetvdbshows')
                    ->select('*')
                    ->where('rating_count', '>=', 10)
                    ->where('fanart', '=', 1)
                    ->whereNotIn('thetvdb_id', $arr)
                    ->orderBy('rating_count', 'desc')
                    ->limit(1)
                    //->limit($lim)                
                    //->offset($lim * $pg - $lim)
                    ->get();
        } else {
            //
            $shows = DB::table('thetvdbshows')
                    ->select('*')
                    ->where('thetvdb_id', '=', $thetvdbId)
                    ->limit(1)
                    ->get();
        }


        $client = new \Adrenth\Thetvdb\Client();
        $token = $client->authentication()->login(THETVDB_API_KEY);
        $client->setToken($token);

        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'he', 'ja', 'pt', 'zh', 'sl', 'hr', 'ko', 'sv', 'no'
        ];

        $badLangs = ['ja', 'he', 'zh', 'ko'];


        foreach ($shows as $show) {

            $thetvdbId = $show->thetvdb_id;
            echo $show->title . ' ' . $show->thetvdb_id . '<br>';

            foreach ($langs as $lang) {

                if ($lang == 'en') {

                    $client->setLanguage($lang);
                    $showData = $client->series()->get($thetvdbId);
                    $data = [
                        'thetvdb_id' => $showData->values['id'],
                        'imdb_id' => $showData->values['imdbId'],
                        'first_aired' => Utils::validDate($showData->values['firstAired']) ? $showData->values['firstAired'] : null,
                        'ended' => $showData->values['status'] == "Ended" ? true : false,
                        'air_day' => date('N', strtotime($showData->values['airsDayOfWeek'])),
                        'air_time' => $showData->values['airsTime'],
                        'rating' => $showData->values['siteRating'],
                        'rating_count' => $showData->values['siteRatingCount'],
                        'runtime' => $showData->values['runtime'],
                        'last_updated' => $showData->values['lastUpdated'],
                    ];

                    $showId = \App\Show::firstOrCreate($data)->id;
                    Utils::insertOption('network', 'select', $lang, $showData->values['network'], $showId, 'App\Show');
                    $translation = [
                        'show_id' => $showId,
                        'lang' => $lang,
                        'title' => $showData->values['seriesName'],
                        'slug' => Utils::slug($showData->values['seriesName']),
                        'content' => $showData->values['overview']
                    ];

                    $translation = \App\ShowTranslation::firstOrCreate(array_filter($translation));

                    $genres = $showData->values['genre'];
                    foreach ($genres as $term) {
                        Utils::insertTerm('genre', $term, 'en', $showId, 'App\Show');
                    }

                    $client->setLanguage('en');

                    try {
                        $actorsData = $client->series()->getActors($thetvdbId)->getData()->all();
                    } catch (\Exception $e) {
                        
                    }

                    if (!empty($actorsData)) {
                        foreach ($actorsData as $actor) {
                            $a = [
                                'thetvdb_id' => $actor->values['id'],
                                'name' => $actor->values['name'],
                                'slug' => Utils::slug($actor->values['name']),
                                'role' => $actor->values['role'],
                                'sort' => $actor->values['sortOrder'],
                                'image' => $actor->values['image'],
                            ];
                            Utils::insertActor($a, $showId, 'App\Show');
                        }
                    }
                    //dd($actorsData);
                    //images
                    $client->setLanguage('en');

                    try {
                        $posters = $client->series()->getImagesWithQuery($thetvdbId, ['keyType' => 'poster'])->getData()->all();
                    } catch (\Exception $e) {
                        
                    }

                    if (!empty($posters)) {
                        foreach ($posters as $poster) {

                            $arr = [
                                'type' => 'poster',
                                'sort' => Utils::score($poster->values['ratingsInfo']['average'], $poster->values['ratingsInfo']['count'], 20),
                                'extension' => 'jpg',
                                'external_patch' => 'http://thetvdb.com/banners/' . $poster->values['fileName'],
                                'model_id' => $showId,
                                'model_type' => 'App\Show'
                            ];

                            $file = File::firstOrCreate($arr);
                        }
                    }

                    $client->setLanguage('en');


                    try {
                        $fanarts = $client->series()->getImagesWithQuery($thetvdbId, ['keyType' => 'fanart'])->getData()->all();
                    } catch (\Exception $e) {
                        
                    }

                    if (!empty($fanarts)) {
                        foreach ($fanarts as $fanart) {

                            $arr = [
                                'type' => 'fanart',
                                'sort' => Utils::score($fanart->values['ratingsInfo']['average'], $fanart->values['ratingsInfo']['count'], 20),
                                'extension' => 'jpg',
                                'external_patch' => 'http://thetvdb.com/banners/' . $fanart->values['fileName'],
                                'model_id' => $showId,
                                'model_type' => 'App\Show'
                            ];
                            $file = File::firstOrCreate($arr);
                        }
                    }

                    $client->setLanguage($lang);

                    $episodes = $client->series()->getEpisodes($thetvdbId);
                    $lastPage = $episodes->values['links']->values['last'];

                    //Episdoes
                    for ($pg = 1; $pg <= $lastPage; $pg++) {
                        $episodes = $client->series()->getEpisodes($thetvdbId, $pg)->getData()->all();
                        foreach ($episodes as $episode) {
                            $e = $client->episodes()->get($episode->values['id']);

                            $episodeArr = [
                                'show_id' => $showId,
                                'thetvdb_id' => $e->values['id'],
                                'imdb_id' => $e->values['imdbId'],
                                'first_aired' => $e->values['firstAired'],
                                'season_number' => $e->values['airedSeason'],
                                'episode_number' => $e->values['airedEpisodeNumber'],
                                'rating' => $e->values['siteRating'],
                                'rating_count' => $e->values['siteRatingCount'],
                                'last_updated' => $e->values['lastUpdated'],
                            ];
                            $episodeId = \App\Episode::firstOrCreate(array_filter($episodeArr))->id;

                            //Images
                            $thumb = 'http://thetvdb.com/banners/' . $e->values['filename'];
                            if ($thumb && !empty($e->values['filename'])) {
                                $arr = [
                                    'type' => 'thumb',
                                    'extension' => 'jpg',
                                    'external_patch' => $thumb = $thumb,
                                    'model_id' => $episodeId,
                                    'model_type' => 'App\Episode'];
                                $file = File::firstOrCreate($arr);
                            }


                            $episodeTranslation = [
                                'episode_id' => $episodeId,
                                'lang' => $lang,
                                'title' => $e->values['episodeName'],
                                //'slug' => Utils::slug($e->values['episodeName']),
                                'content' => $e->values['overview'],
                            ];
                            \App\EpisodeTranslation::firstOrCreate(array_filter($episodeTranslation));
                        }
                    }
                } else {

                    $client->setLanguage($lang);
                    $showData = $client->series()->get($thetvdbId);

                    $translation = [
                        'show_id' => $showId,
                        'lang' => $lang,
                        'title' => $showData->values['seriesName'],
                        'slug' => Utils::slug($showData->values['seriesName']),
                        'content' => $showData->values['overview']
                    ];

                    //
                    if (in_array($lang, $badLangs)) {
                        $enTitle = DB::table('shows_translations')->select('title')->where('show_id', $showId)->where('lang', 'en')->first()->title;
                        if (!empty($enTitle)) {
                            $translation['slug'] = Utils::slug($enTitle);
                        }
                    }

                    $translation = \App\ShowTranslation::firstOrCreate(array_filter($translation));


                    $client->setLanguage($lang);

                    $episodes = $client->series()->getEpisodes($thetvdbId);
                    $lastPage = $episodes->values['links']->values['last'];
                    //Episdoes
                    for ($pg = 1; $pg <= $lastPage; $pg++) {
                        $episodes = $client->series()->getEpisodes($thetvdbId, $pg)->getData()->all();
                        foreach ($episodes as $episode) {

                            //$e = $client->episodes()->get($episode->values['id']);

                            $episodeArr = [
                                'show_id' => $showId,
                                'thetvdb_id' => $episode->values['id'],
                            ];
                            $episodeId = \App\Episode::firstOrCreate(array_filter($episodeArr))->id;

                            $episodeTranslation = [
                                'episode_id' => $episodeId,
                                'lang' => $lang,
                                'title' => $episode->values['episodeName'],
                                //'slug' => Utils::slug($episode->values['episodeName']),
                                'content' => $episode->values['overview'],
                            ];
                            \App\EpisodeTranslation::firstOrCreate(array_filter($episodeTranslation));
                        }
                    }
                }
            }
        }
        $end = microtime(true);

        echo 'Elapsed time ' . date("H:i:s", ($end - $start));
    }

    public function updateShow($theTvDbId) {

        ini_set('max_execution_time', 60 * 60 * 10);

        $client = new \Adrenth\Thetvdb\Client();
        $token = $client->authentication()->login(THETVDB_API_KEY);
        $client->setToken($token);

        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'ru',
                //'pl', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'he', 'ja', 'pt', 'zh', 'sl', 'hr', 'ko', 'sv', 'no'
        ];

        $badLangs = ['ja', 'he', 'zh', 'ko'];

        foreach ($langs as $lang) {

            if ($lang == DEF_TRANSLATION) {
                $client->setLanguage($lang);
                $showData = $client->series()->get($theTvDbId);

                $dbLastUpdated = \App\Show::where('thetvdb_id', '=', $theTvDbId)->first()->last_updated;


                if (!($showData->values['lastUpdated'] > $dbLastUpdated)) {
                    //dd('do not update... ' . $showData->values['lastUpdated'] . ' !bigger than ' . $dbLastUpdated);
                }

                $data = [
                    'thetvdb_id' => $showData->values['id'],
                    'imdb_id' => $showData->values['imdbId'],
                    'first_aired' => Utils::validDate($showData->values['firstAired']) ? $showData->values['firstAired'] : null,
                    'ended' => $showData->values['status'] == "Ended" ? true : false,
                    'air_day' => date('N', strtotime($showData->values['airsDayOfWeek'])),
                    'air_time' => $showData->values['airsTime'],
                    'rating' => $showData->values['siteRating'],
                    'rating_count' => $showData->values['siteRatingCount'],
                    'runtime' => $showData->values['runtime'],
                    'last_updated' => $showData->values['lastUpdated'],
                ];

                $showId = \App\Show::updateOrCreate(
                                ['thetvdb_id' => $theTvDbId], $data
                        )->id;

                Utils::insertOption('network', 'select', $lang, $showData->values['network'], $showId, 'App\Show');

                $translation = [
                    'show_id' => $showId,
                    'lang' => $lang,
                    'title' => $showData->values['seriesName'],
                    'slug' => Utils::slug($showData->values['seriesName']),
                    'content' => $showData->values['overview']
                ];

                $translation = \App\ShowTranslation::updateOrCreate(
                                ['show_id' => $showId,
                            'lang' => $lang], $translation);

                $genres = $showData->values['genre'];
                foreach ($genres as $term) {

                    Utils::insertTerm('genre', $term, 'en', $showId, 'App\Show');
                }

                $client->setLanguage('en');
                $actorsData = [];
                try {
                    $actorsData = $client->series()->getActors($theTvDbId)->getData()->all();
                } catch (\Exception $e) {
                    
                }
                if (isset($actorsData) && !empty($actorsData)) {
                    foreach ($actorsData as $actor) {
                        $a = [
                            'thetvdb_id' => $actor->values['id'],
                            'name' => $actor->values['name'],
                            'slug' => Utils::slug($actor->values['name']),
                            'role' => $actor->values['role'],
                            'sort' => $actor->values['sortOrder'],
                            'image' => $actor->values['image'],
                        ];
                        Utils::insertActor($a, $showId, 'App\Show');
                    }
                }

                //dd($actorsData);
                //images
                $client->setLanguage('en');
                $posters = [];
                try {
                    $posters = $client->series()->getImagesWithQuery($theTvDbId, ['keyType' => 'poster'])->getData()->all();
                } catch (\Exception $e) {
                    
                }
                if (!empty($posters)) {
                    foreach ($posters as $poster) {
                        $arr = [
                            'type' => 'poster',
                            'sort' => round($poster->values['ratingsInfo']['average']), //Utils::score($fanart->values['ratingsInfo']['average'], $fanart->values['ratingsInfo']['count'], 20)
                            'extension' => 'jpg',
                            'external_patch' => 'http://thetvdb.com/banners/' . $poster->values['fileName'],
                            'model_id' => $showId,
                            'model_type' => 'App\Show'
                        ];
                        $file = \App\File::updateOrCreate(['external_patch' => 'http://thetvdb.com/banners/' . $poster->values['fileName']], $arr);
                    }
                }

                $client->setLanguage('en');

                $fanarts = [];
                try {
                    $fanarts = $client->series()->getImagesWithQuery($theTvDbId, ['keyType' => 'fanart'])->getData()->all();
                } catch (\Exception $e) {
                    
                }
                if (!empty($fanarts)) {
                    foreach ($fanarts as $fanart) {

                        $arr = [
                            'type' => 'fanart',
                            'sort' => round($fanart->values['ratingsInfo']['average']), //Utils::score($fanart->values['ratingsInfo']['average'], $fanart->values['ratingsInfo']['count'], 20)
                            'extension' => 'jpg',
                            'external_patch' => 'http://thetvdb.com/banners/' . $fanart->values['fileName'],
                            'model_id' => $showId,
                            'model_type' => 'App\Show'
                        ];

                        $file = \App\File::updateOrCreate(['external_patch' => 'http://thetvdb.com/banners/' . $fanart->values['fileName']], $arr);
                    }
                }

                $client->setLanguage($lang);

                $episodes = $client->series()->getEpisodes($theTvDbId);
                $lastPage = $episodes->values['links']->values['last'];

                //Episodes
                for ($pg = 1; $pg <= $lastPage; $pg++) {
                    $episodes = $client->series()->getEpisodes($theTvDbId, $pg)->getData()->all();
                    //dd($episodes);
                    foreach ($episodes as $episode) {
                        $e = $client->episodes()->get($episode->values['id']);

                        $episodeArr = [
                            'show_id' => $showId,
                            'thetvdb_id' => $e->values['id'],
                            'imdb_id' => $e->values['imdbId'],
                            'first_aired' => Utils::validDate($e->values['firstAired']) ? $e->values['firstAired'] : null,
                            'season_number' => $e->values['airedSeason'],
                            'episode_number' => $e->values['airedEpisodeNumber'],
                            'rating' => $e->values['siteRating'],
                            'rating_count' => $e->values['siteRatingCount'],
                            'last_updated' => $e->values['lastUpdated'],
                        ];

                        $episodeId = \App\Episode::updateOrCreate(
                                        ['show_id' => $showId,
                                    'thetvdb_id' => $e->values['id']], array_filter($episodeArr))->id;


                        //Images
                        $thumb = 'http://thetvdb.com/banners/' . $e->values['filename'];
                        if ($thumb) {
                            $arr = [
                                'type' => 'thumb',
                                'extension' => 'jpg',
                                'external_patch' => $thumb,
                                'model_id' => $episodeId,
                                'model_type' => 'App\Episode'];

                            $file = \App\File::updateOrCreate(['external_patch' => $thumb], $arr);
                        }


                        $episodeTranslation = [
                            'episode_id' => $episodeId,
                            'lang' => $lang,
                            'title' => $e->values['episodeName'],
                            //'slug' => Utils::slug($e->values['episodeName']),
                            'content' => $e->values['overview'],
                        ];


                        \App\EpisodeTranslation::updateOrCreate(
                                ['episode_id' => $episodeId,
                            'lang' => $lang], $episodeTranslation);
                    }
                }
            } else {
                //Other languages
                $client->setLanguage($lang);
                $showData = $client->series()->get($theTvDbId);

                $showId = \App\Show::where('thetvdb_id', $theTvDbId)->first()->id;

                $translation = [
                    'show_id' => $showId,
                    'lang' => $lang,
                    'title' => $showData->values['seriesName'],
                    'slug' => Utils::slug($showData->values['seriesName']),
                    'content' => $showData->values['overview']
                ];

                if (in_array($lang, $badLangs)) {
                    $enTitle = DB::table('shows_translations')->select('title')->where('show_id', $showId)->where('lang', 'en')->first()->title;
                    if (!empty($enTitle)) {
                        $translation['slug'] = Utils::slug($enTitle);
                    }
                }


                $translation = \App\ShowTranslation::updateOrCreate(
                                ['show_id' => $showId,
                            'lang' => $lang], $translation);


                $client->setLanguage($lang);

                $episodes = $client->series()->getEpisodes($theTvDbId);
                $lastPage = $episodes->values['links']->values['last'];
                //Episdoes
                for ($pg = 1; $pg <= $lastPage; $pg++) {
                    $episodes = $client->series()->getEpisodes($theTvDbId, $pg)->getData()->all();
                    foreach ($episodes as $episode) {

                        $e = $client->episodes()->get($episode->values['id']);

                        $episodeArr = [
                            'show_id' => $showId,
                            'thetvdb_id' => $e->values['id'],
                            'imdb_id' => $e->values['imdbId'],
                            'first_aired' => Utils::validDate($e->values['firstAired']) ? $e->values['firstAired'] : null,
                            'season_number' => $e->values['airedSeason'],
                            'episode_number' => $e->values['airedEpisodeNumber'],
                            'rating' => $e->values['siteRating'],
                            'rating_count' => $e->values['siteRatingCount'],
                            'last_updated' => $e->values['lastUpdated'],
                        ];


                        $episodeId = \App\Episode::updateOrCreate(
                                        ['show_id' => $showId,
                                    'thetvdb_id' => $episode->values['id']], array_filter($episodeArr))->id;

                        $episodeTranslation = [
                            'episode_id' => $episodeId,
                            'lang' => $lang,
                            'title' => $episode->values['episodeName'],
                            //'slug' => Utils::slug($episode->values['episodeName']),
                            'content' => $episode->values['overview'],
                        ];


                        \App\EpisodeTranslation::updateOrCreate(
                                ['episode_id' => $episodeId,
                            'lang' => $lang], $episodeTranslation);
                    }
                }
            }
        }
    }

    public function images() {

        //Than update 
        //UPDATE files SET patch=CONCAT('/',patch) WHERE patch NOT LIKE '/%';
        $images = \App\File::where('patch', NULL)->orderBy('id', 'asc')->limit(1000)->get();


        foreach ($images as $img) {

            if (isset($img->external_patch)) {
                if (strpos($img->external_patch, 'http') !== false) {

                    $headers = get_headers($img->external_patch, true);

                    if ($headers[0] == 'HTTP/1.1 200 OK') {
                        $ext = pathinfo($img->external_patch, PATHINFO_EXTENSION);
                        if (!isset($ext) or empty($ext)) {
                            if (isset($headers['Content-Type'])) {
                                $pieces = explode('/', $headers['Content-Type']);
                                $ext = strtok(end($pieces), '+');
                            } else {
                                $ext = 'jpg';
                            }
                        }
                        $ext = strtok($ext, '?');

                        $patch = 'public/img/' .
                                strtolower(str_replace("\\", '', str_replace('App', '', $img->model_type))) . 's/' .
                                $img->type . '-' . $img->model_id . '-' . $img->id .
                                '.' . $ext;

                        try {
                            file_put_contents($patch, file_get_contents($img->external_patch));
                            $file = \App\File::find($img->id);
                            $file->patch = $patch;
                            $file->extension = $ext;
                            $file->file_size = $headers['Content-Length'];
                            $file->save();
                            echo $patch . "<br/>";
                        } catch (\Exception $e) {
                            
                        }
                    }
                } else {
                    echo '<strong style="color:red">' . $img->id . ' ' . $img->external_patch . "</strong><br/>";
                }
            }

            //echo $img->id . ' ' . $img->model_type . '' . $img->model_id . '' . $img->type . "<br>";
        }
    }

    public function search(Request $request = null, $q = null) {
        header("Content-type:application/json");

        $lang = isset($_GET['lang']) ? $_GET['lang'] : DEF_LANG;
        $query = isset($_GET['q']) ? $_GET['q'] : null;
        if ($q !== null) {
            $query = $q;
        }

        $shows = new Show;
        if (isset($query)) {
            $shows = $shows->join('shows_translations as translation', 'translation.show_id', '=', 'shows.id');
            $shows = $shows->where('translation.title', 'LIKE', "%$query%");
            $shows = $shows->select('shows.*')->distinct();
            $shows = $shows->limit(5)->get();
        }
        $results = null;
        foreach ($shows as $show) {
            $results[] = [
                'name' => $show->translation($lang)->title,
                'icon' => $show->poster() !== null ? $show->poster()->src() : null,
                'url' => $show->url($lang),
            ];
        }

        if (!$results) {
            $results[] = [
                'name' => 'Nic nebylo nalezeno',
                'url' => null,
            ];
        }

        echo json_encode($results, JSON_PRETTY_PRINT);
        exit;
    }

    //New Api
    public function importNew($thetvdbId = null) {
        ini_set('max_execution_time', 60 * 60 * 10);
        $start = microtime(true);
        $shows = null;
        if ($thetvdbId == null) {

            $showsInDb = DB::table('shows')->select('thetvdb_id')->get()->toArray();
            $arr = [];
            foreach ($showsInDb as $s) {
                $arr[] = $s->thetvdb_id;
            }

            $shows = DB::table('thetvdbshows')
                    ->select('*')
                    ->where('rating_count', '>=', 20)
                    ->where('fanart', '=', 1)
                    ->where('poster', '=', 1)
                    ->whereNotIn('thetvdb_id', $arr)
                    ->orderBy('rating_count', 'desc')
                    ->limit(1)
                    ->get();
        } else {
            $shows = DB::table('thetvdbshows')
                    ->select('*')
                    ->where('thetvdb_id', '=', $thetvdbId)
                    ->limit(1)
                    ->get();
        }

        $client = new \Adrenth\Thetvdb\Client();
        $token = $client->authentication()->login(THETVDB_API_KEY);
        $client->setToken($token);


        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'he', 'ja', 'pt', 'zh', 'sl', 'hr', 'ko', 'sv', 'no'
        ];

        $show = [
            'data' => [
                'thetvdb_id' => '',
            ],
            'images' => [
                'fanarts' => [],
                'posters' => [],
            ],
            'options' => [
                'network' => []
            ],
            'actors' => [
            ],
            'genres' => [
            ],
            'translations' => [
                'cs' => [],
                'en' => [],
            ],
            'episodes' => [
                'data' => [
                ],
                'translations' => [
                    'en' => [
                    ]
                ],
            ],
        ];

        foreach ($shows as $show) {
            $thetvdbId = $show->thetvdb_id;
            echo $show->title . ' ' . $show->thetvdb_id . '<br>';

            foreach ($langs as $lang) {
                $client->setLanguage($lang);
                $showData = $client->series()->get($thetvdbId);

                dump($showData);
                $data = [
                    'thetvdb_id' => $showData->values['id'],
                    'imdb_id' => $showData->values['imdbId'],
                    'first_aired' => Utils::validDate($showData->values['firstAired']) ? $showData->values['firstAired'] : null,
                    'ended' => $showData->values['status'] == "Ended" ? true : false,
                    'air_day' => date('N', strtotime($showData->values['airsDayOfWeek'])),
                    'air_time' => $showData->values['airsTime'],
                    'rating' => $showData->values['siteRating'],
                    'rating_count' => $showData->values['siteRatingCount'],
                    'runtime' => $showData->values['runtime'],
                    'last_updated' => $showData->values['lastUpdated'],
                ];
                $showId = uniqid();
                //$showId = \App\Show::firstOrCreate($data)->id;
                //Utils::insertOption('network', 'select', $lang, $showData->values['network'], $showId, 'App\Show');
                $translation = [
                    'show_id' => $showId,
                    'lang' => $lang,
                    'title' => $showData->values['seriesName'],
                    'slug' => Utils::slug($showData->values['seriesName']),
                    'content' => $showData->values['overview']
                ];

                dump($translation);

                //$translation = \App\ShowTranslation::firstOrCreate(array_filter($translation));

                $genres = $showData->values['genre'];
                foreach ($genres as $term) {
                    //Utils::insertTerm('genre', $term, 'en', $showId, 'App\Show');
                }
                dump($genres);

                $client->setLanguage('en');

                try {
                    $actorsData = $client->series()->getActors($thetvdbId)->getData()->all();
                } catch (\Exception $e) {
                    
                }

                if (!empty($actorsData)) {
                    foreach ($actorsData as $actor) {
                        $a = [
                            'thetvdb_id' => $actor->values['id'],
                            'name' => $actor->values['name'],
                            'slug' => Utils::slug($actor->values['name']),
                            'role' => $actor->values['role'],
                            'sort' => $actor->values['sortOrder'],
                            'image' => $actor->values['image'],
                        ];
                        //Utils::insertActor($a, $showId, 'App\Show');
                    }
                }

                dump($actorsData);

                $client->setLanguage('en');
                $posters = $client->series()->getImagesWithQuery($thetvdbId, ['keyType' => 'poster'])->getData()->all();
                if (!empty($posters)) {
                    foreach ($posters as $poster) {

                        $arr = [
                            'type' => 'poster',
                            'sort' => Utils::score($poster->values['ratingsInfo']['average'], $poster->values['ratingsInfo']['count'], 20),
                            'extension' => 'jpg',
                            'external_patch' => 'http://thetvdb.com/banners/' . $poster->values['fileName'],
                            'model_id' => $showId,
                            'model_type' => 'App\Show'
                        ];

                        //$file = File::firstOrCreate($arr);
                    }
                }
                dump($posters);
                $client->setLanguage('en');


                try {
                    $fanarts = $client->series()->getImagesWithQuery($thetvdbId, ['keyType' => 'fanart'])->getData()->all();
                } catch (\Exception $e) {
                    
                }
                dump($fanarts);
                if (!empty($fanarts)) {
                    foreach ($fanarts as $fanart) {

                        $arr = [
                            'type' => 'fanart',
                            'sort' => Utils::score($fanart->values['ratingsInfo']['average'], $fanart->values['ratingsInfo']['count'], 20),
                            'extension' => 'jpg',
                            'external_patch' => 'http://thetvdb.com/banners/' . $fanart->values['fileName'],
                            'model_id' => $showId,
                            'model_type' => 'App\Show'
                        ];
                        //$file = File::firstOrCreate($arr);
                    }
                }

                $client->setLanguage($lang);

                $episodes = $client->series()->getEpisodes($thetvdbId);
                $lastPage = $episodes->values['links']->values['last'];
                dd($episodes);

                //Episdoes
                for ($pg = 1; $pg <= $lastPage; $pg++) {
                    $episodes = $client->series()->getEpisodes($thetvdbId, $pg)->getData()->all();
                    foreach ($episodes as $episode) {
                        $e = $client->episodes()->get($episode->values['id']);

                        $episodeArr = [
                            'show_id' => $showId,
                            'thetvdb_id' => $e->values['id'],
                            'imdb_id' => $e->values['imdbId'],
                            'first_aired' => $e->values['firstAired'],
                            'season_number' => $e->values['airedSeason'],
                            'episode_number' => $e->values['airedEpisodeNumber'],
                            'rating' => $e->values['siteRating'],
                            'rating_count' => $e->values['siteRatingCount'],
                            'last_updated' => $e->values['lastUpdated'],
                        ];
                        //$episodeId = \App\Episode::firstOrCreate(array_filter($episodeArr))->id;
                        //Images
                        $thumb = 'http://thetvdb.com/banners/' . $e->values['filename'];
                        if ($thumb) {
                            $arr = [
                                'type' => 'thumb',
                                'extension' => 'jpg',
                                'external_patch' => $thumb = $thumb,
                                'model_id' => $episodeId,
                                'model_type' => 'App\Episode'];
                            //$file = File::firstOrCreate($arr);
                        }


                        $episodeTranslation = [
                            'episode_id' => $episodeId,
                            'lang' => $lang,
                            'title' => $e->values['episodeName'],
                            'slug' => Utils::slug($e->values['episodeName']),
                            'content' => $e->values['overview'],
                        ];
                        //\App\EpisodeTranslation::firstOrCreate(array_filter($episodeTranslation));
                    }
                }
            }
        }
        $end = microtime(true);

        dd('Elapsed time ' . date("H:i:s", ($end - $start)));
    }

    public function updateSlugs() {

        $translations = \App\ShowTranslation::all();
        $bad = ['ja', 'he', 'zh', 'ko']; //replace with en or something better en default...
        $slugify = new Slugify();

        foreach ($translations as $t) {

            $slug = $slugify->slugify($t->title);

            if (in_array($t->lang, $bad)) {
                $enTitle = DB::table('shows_translations')->select('title')->where('show_id', $t->show_id)->where('lang', 'en')->first()->title;
                if (!empty($enTitle)) {
                    $slug = $slugify->slugify($enTitle);
                }
            }


            if ($t->slug != $slug) {
                echo "<span style='color:blue'>";
            }
            echo $t->lang . " old:" . $t->title . ' as ' . $t->slug . ' new:' . $slug;
            if ($t->slug != $slug) {
                echo "</span>";
            }
            echo "<br/>";

            if ($t->slug != $slug) {
                DB::table('shows_translations')->where('id', $t->id)->update(['slug' => $slug]);
            }
        }
    }

}
