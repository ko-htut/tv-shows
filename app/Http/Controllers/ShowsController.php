<?php

namespace App\Http\Controllers;

use App\Language;
use App\Term;
use App\File;
use App\Show;
use App\Select;
use DB;
use App\Functions\Utils;
use View;
use Image;
use Illuminate\Support\Facades\Route;

define('THETVDB_API_KEY', '5EA3012696C40059');

class ShowsController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function index($lang = DEF_LANG) {


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
                'rating' => 'Rating',
            ],
        ];

        $limit = 2 * 3 * 5;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $next_page = $page + 1;

        $shows = new Show;
        //Search
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $shows = $shows->join('shows_translations as translation', 'translation.show_id', '=', 'shows.id');
            $shows = $shows->where('translation.title', 'LIKE', "%{$_GET['search']}%");
            $shows = $shows->where('translation.lang', '=', $lang); ////All translations
        }
        //Terms
        if (isset($_GET['genre'])) {
            $shows = $shows->join('terms_to_models as ttm', 'ttm.model_id', '=', 'shows.id');
            $shows = $shows->whereIn('ttm.term_id', $_GET['genre']);
        }
        //Options
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

        //Range
        if (isset($_GET['sMin']) && isset($_GET['sMax'])) {
            $shows = $shows->whereBetween('runtime', [$_GET['sMin'], $_GET['sMax']]);
        }

        $shows = $shows->select('shows.*')->distinct('shows.id');


        $results = $shows->count('shows.id'); //'shows.id'
        //$shows->orderBy('id', 'DESC');
        //Order
        if (isset($_GET['order']) && !empty($_GET['order'])) {
            $shows = $shows->orderBy($_GET['order'], 'DESC');
        }


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
                $snippets = [$_GET['page'] == 1 ? 'snippet-wrapper' : 'snippet-more' => $view];
                print json_encode(['snippets' => $snippets]);
                exit();
            }
        }

        return view('shows.listing', compact(['filter', 'shows', 'lang', 'page', 'next_page', 'more', 'results']));
    }

    public function detailTranslate($lang = null, $slug = null) {

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

        $season = null;
        $seasonNum = null;
        if (Utils::isAjax() && isset($_GET['season'])) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            $season = $show->seasonEpisodes($_GET['season']);
            $view = View::make('shows.ajax.season', compact(['season', 'lang']))->render();
            $snippets = ['snippet-season-' . $_GET['season'] => $view];
            print json_encode(['snippets' => $snippets]);
            exit();
        } else if (isset($_GET['season'])) {
            $season = $show->seasonEpisodes($_GET['season']);
            $seasonNum = $_GET['season'];
        }

        return view('shows.detail', compact(['show', 'lang', 'season', 'seasonNum']));
    }

    //TODO COPY
    public function detail($slug) {
        $lang = DEF_LANG;
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

        if (isset($_GET['update']) && $_GET['update'] == 'true') {
            $c = new ShowsController;
            $c->updateShow($show->thetvdb_id);
        }

        $season = null;
        $seasonNum = null;
        if (Utils::isAjax() && isset($_GET['season'])) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            $season = $show->seasonEpisodes($_GET['season']);
            $view = View::make('shows.ajax.season', compact(['season', 'lang']))->render();
            $snippets = ['snippet-season-' . $_GET['season'] => $view];
            print json_encode(['snippets' => $snippets]);
            exit();
        } else if (isset($_GET['season'])) {
            $season = $show->seasonEpisodes($_GET['season']);
            $seasonNum = $_GET['season'];
        }

        return view('shows.detail', compact(['show', 'lang', 'season', 'seasonNum']));
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

    public function import() {

        ini_set('max_execution_time', 60 * 60 * 10);
        //$langs_xml = file_get_contents("http://thetvdb.com/api/5EA3012696C40059/languages.xml");
        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'he', 'ja', 'pt', 'zh', 'sl', 'hr', 'ko', 'sv', 'no'
        ];

        $shows = DB::table('thetvdbshows')
                ->select('thetvdb_id')
                ->where('rating_count', '>=', 50)
                ->where('fanart', '=', 1)
                ->where('poster', '=', 1)
                ->get();
        // dd($shows);

        foreach ($shows as $item) {

            $id = $item->thetvdb_id;
            $enTitle = null;
            $enContent = null;
            $showId = null;

            foreach ($langs as $lang) {
                $c = 0;
                $xml = null;
                $data = null;
                $error = false;
                while (!$data) {
                    $data = file_get_contents("http://thetvdb.com/api/" . THETVDB_API_KEY . "/series/$id/all/$lang.xml");
                    libxml_use_internal_errors(true);
                    $doc = simplexml_load_string($data);
                    $xml = explode("\n", $data);
                    if (!$doc) {
                        $errors = libxml_get_errors();
                        libxml_clear_errors();
                        $error = true;
                        break;
                    } else {
                        $xml = simplexml_load_string($data);
                    }



                    $c++;
                    if ($c == 5) {
                        $error = true;
                        echo "Too many requests.</br>";
                        break;
                    }
                }

                if ($error)
                    break;


                Language::firstOrCreate(['code' => $lang]);

                if ($lang == "en") {
                    $enTitle = trim($xml->Series->SeriesName);
                    $enContent = trim($xml->Series->Overview);
                    $show = [
                        'thetvdb_id' => trim($xml->Series->id),
                        'imdb_id' => trim($xml->Series->IMDB_ID),
                        'first_aired' => trim($xml->Series->FirstAired),
                        'finale_aired' => trim($xml->Series->finale_aired),
                        'air_day' => date('N', strtotime(trim($xml->Series->Airs_DayOfWeek))),
                        'air_time' => trim($xml->Series->Airs_Time),
                        'rating' => trim($xml->Series->Rating),
                        'rating_count' => trim($xml->Series->RatingCount),
                        'runtime' => trim($xml->Series->Runtime),
                        'last_updated' => trim($xml->Series->lastupdated),
                    ];
                    $showId = \App\Show::firstOrCreate(array_filter($show))->id;

                    //Options
                    Utils::insertOption('status', 'select', $lang, trim($xml->Series->Status), $showId, 'App\Show');
                    Utils::insertOption('network', 'select', $lang, trim($xml->Series->Network), $showId, 'App\Show');
                    Utils::insertOption('content_rating', 'select', $lang, trim($xml->Series->ContentRating), $showId, 'App\Show');

                    //Terms
                    $terms = array_filter(explode('|', trim($xml->Series->Genre)));
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            Utils::insertTerm('genre', $term, 'en', $showId, 'App\Show');
                        }
                    }

                    //Actors
                    $actors = array_filter(explode('|', trim($xml->Series->Actors)));
                    if (!empty($actors)) {
                        foreach ($actors as $name) {
                            Utils::insertActor($name, $showId, 'App\Show');
                        }
                    }

                    //Images
                    $fanart = trim($xml->Series->fanart);
                    if (!empty($fanart)) {
                        $fanart = 'http://thetvdb.com/banners/' . $fanart;
                        $arr = ['type' => 'fanart', 'extension' => 'jpg', 'external_patch' => $fanart, 'model_id' => $showId, 'model_type' => 'App\Show'];
                        $file = File::firstOrCreate($arr);
                    }

                    $poster = trim($xml->Series->poster);
                    if (!empty($poster)) {
                        $poster = 'http://thetvdb.com/banners/' . $poster;
                        $arr = ['type' => 'poster', 'extension' => 'jpg', 'external_patch' => $poster, 'model_id' => $showId, 'model_type' => 'App\Show'];
                        $file = File::firstOrCreate($arr);
                    }

                    $translation = [
                        'show_id' => $showId,
                        'lang' => $lang,
                        'title' => trim($xml->Series->SeriesName),
                        'slug' => Utils::slug(trim($xml->Series->SeriesName)),
                        'content' => trim($xml->Series->Overview),
                    ];
                    $translation = \App\ShowTranslation::firstOrCreate(array_filter($translation));

                    //Episodes import
                    $episodes = $xml->Episode;
                    foreach ($episodes as $episode) {
                        $episodeArr = [
                            'show_id' => $showId,
                            'thetvdb_id' => trim($episode->id),
                            'imdb_id' => trim($episode->IMDB_ID),
                            'first_aired' => trim($episode->FirstAired),
                            'season_number' => trim($episode->SeasonNumber),
                            'episode_number' => trim($episode->EpisodeNumber),
                            'rating' => trim($episode->Rating),
                            'rating_count' => trim($episode->RatingCount),
                            'last_updated' => trim($episode->lastupdated),
                        ];
                        $episodeId = \App\Episode::firstOrCreate(array_filter($episodeArr))->id;

                        //Images
                        $thumb = trim($episode->filename);
                        if (!empty($thumb)) {
                            $thumb = 'http://thetvdb.com/banners/' . $thumb;
                            $arr = ['type' => 'thumb', 'extension' => 'jpg', 'external_patch' => $thumb, 'model_id' => $episodeId, 'model_type' => 'App\Episode'];
                            $file = File::firstOrCreate($arr);
                        }

                        $episodeTranslation = [
                            'episode_id' => $episodeId,
                            'lang' => $lang,
                            'title' => trim($episode->EpisodeName),
                            'slug' => Utils::slug(trim($episode->EpisodeName)),
                            'content' => trim($episode->Overview),
                        ];
                        \App\EpisodeTranslation::firstOrCreate(array_filter($episodeTranslation));
                    }
                } else {
                    $title = trim($xml->Series->SeriesName);
                    $content = null;
                    if (substr($enContent, 0, min(100, strlen($enContent))) != substr(trim($xml->Series->Overview), 0, min(100, strlen(trim($xml->Series->Overview))))) {
                        $content = trim($xml->Series->Overview);
                    }
                    $translation = [
                        'show_id' => $showId,
                        'lang' => $lang,
                        'title' => $title,
                        'slug' => Utils::slug($title),
                        'content' => $content,
                    ];
                    \App\ShowTranslation::firstOrCreate(array_filter($translation));
                    //Episodes import
                    $episodes = $xml->Episode;
                    foreach ($episodes as $episode) {
                        $title = null;
                        $content = null;
                        $episodeArr = [
                            'show_id' => $showId,
                            'thetvdb_id' => trim($episode->id),
                        ];
                        $episodeId = \App\Episode::firstOrCreate(array_filter($episodeArr))->id;
                        $dbEpisode = DB::table('episodes_translations')
                                ->where('lang', 'en')
                                ->where('episode_id', $episodeId)
                                ->first();

                        $title = trim($episode->EpisodeName);

                        $enContent = $dbEpisode->content;
                        $content = null;
                        if (substr($enContent, 0, min(100, strlen($enContent))) != substr(trim($episode->Overview), 0, min(100, strlen($episode->Overview)))) {
                            $content = trim($episode->Overview);
                        }

                        $episodeTranslation = [
                            'episode_id' => $episodeId,
                            'lang' => $lang,
                            'title' => $title,
                            'slug' => Utils::slug($title),
                            'content' => $content,
                        ];
                        \App\EpisodeTranslation::firstOrCreate(array_filter($episodeTranslation));
                    }
                }
            }
        }
    }

    //New Api
    public function import2() {
        //ini_set('max_execution_time', 60 * 60 * 10);
        //$pg = 8;
        //$lim = 60;

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
                //->limit($lim)                
                //->offset($lim * $pg - $lim)
                ->get();
        
        //dd($shows);

        $client = new \Adrenth\Thetvdb\Client();
        $token = $client->authentication()->login(THETVDB_API_KEY);
        $client->setToken($token);

        $langs = $client->languages()->all()->getData()->all();

        foreach ($langs as $lang) {
            $lng = [
                'code' => $lang->values['abbreviation'],
                'name' => $lang->values['name'],
                'englishName' => $lang->values['englishName']
            ];
            Language::firstOrCreate($lng);
        }

        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'he', 'ja', 'pt', 'zh', 'sl', 'hr', 'ko', 'sv', 'no'
        ];

        foreach ($shows as $show) {
            $thetvdbId = $show->thetvdb_id;
            foreach ($langs as $lang) {

                if ($lang == 'en') {
                    $client->setLanguage($lang);
                    $showData = $client->series()->get($thetvdbId);
                    $data = [
                        'thetvdb_id' => $showData->values['id'],
                        'imdb_id' => $showData->values['imdbId'],
                        'first_aired' => $showData->values['firstAired'],
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
                    $actorsData = $client->series()->getActors($thetvdbId)->getData()->all();
                        
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

                            $file = File::firstOrCreate($arr);
                        }
                    }

                    $client->setLanguage('en');
                    $fanarts = $client->series()->getImagesWithQuery($thetvdbId, ['keyType' => 'fanart'])->getData()->all();
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
                            if ($thumb) {
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
                                'slug' => Utils::slug($e->values['episodeName']),
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
                                'slug' => Utils::slug($episode->values['episodeName']),
                                'content' => $episode->values['overview'],
                            ];
                            \App\EpisodeTranslation::firstOrCreate(array_filter($episodeTranslation));
                        }
                    }
                }
            }
        }
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
                $actorsData = $client->series()->getActors($theTvDbId)->getData()->all();

                if (!empty($actorsData)) {
                    foreach ($actorsData as $actor) {
                        $a = [
                            'thetvdb_id' => $actor->values['id'],
                            'name' => $actor->values['name'],
                            'slug' => $actor->values['name'],
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
                $posters = $client->series()->getImagesWithQuery($theTvDbId, ['keyType' => 'poster'])->getData()->all();
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
                $fanarts = $client->series()->getImagesWithQuery($theTvDbId, ['keyType' => 'fanart'])->getData()->all();
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
                            'slug' => Utils::slug($e->values['episodeName']),
                            'content' => $e->values['overview'],
                        ];


                        \App\EpisodeTranslation::updateOrCreate(
                                ['episode_id' => $episodeId,
                            'lang' => $lang], $episodeTranslation);
                    }
                }
            } else {

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
                            'slug' => Utils::slug($episode->values['episodeName']),
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
                        } catch (Exception $e) {
                            
                        }
                    }
                } else {
                    echo '<strong style="color:red">' . $img->id . ' ' . $img->external_patch . "</strong><br/>";
                }
            }

            //echo $img->id . ' ' . $img->model_type . '' . $img->model_id . '' . $img->type . "<br>";
        }
    }

    public function search($query, $lang) {
        $shows = new Show;
        if (isset($query)) {
            //Search
            $shows = $shows->join('shows_translations as translation', 'translation.show_id', '=', 'shows.id');
            $shows = $shows->where('translation.title', 'LIKE', "%$query%");
            //$shows = $shows->where('translation.lang', '=', $lang); ////All translations
            $shows = $shows->select('shows.*')->distinct();
            $shows = $shows->paginate(100);
        }
        $result = null;
        foreach ($shows as $show) {
            $name = $show->translation($lang)->title;
            $img = $show->poster()->external_patch;
            $result[$name] = $img;
        }
        return json_encode($result);
    }

}
