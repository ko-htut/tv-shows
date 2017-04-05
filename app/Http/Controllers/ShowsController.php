<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Language;
use App\Show;
use App\Select;
use App\Option;
use App\OptionTranslation;
use App\OptionPivot;
use App\File;
use App\FileTranslation;
use Validator;
use Redirect;
use Cookie;
use DB;
use App\Functions\Utils;
use Lang;
use View;

class ShowsController extends Controller {

    public function listing($lang = 'cs') {

        $shows = Show::paginate(6);
        
        $filter = [
            'genres' => \App\Term::all(),
            'networks' => \App\Option::where('select_id', '=', Select::where('title', '=', 'network')->first()->id)->get(),
            'statuses' => \App\Option::where('select_id', '=', Select::where('title', '=', 'status')->first()->id)->get(),
        ];


        //Filter
        if (Utils::isAjax()) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");

            $shows = new Show;

            //Terms
            if (isset($_GET['genre'])) {
                $shows = $shows->join('terms_to_models as ttm', 'ttm.model_id', '=', 'shows.id');
                $shows = $shows->whereIn('term_id', $_GET['genre']);
            }

            //Options
            $optionsKeys = ['network', 'status'];
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
            $shows = $shows->select('shows.*')->distinct();
            $shows = $shows->paginate(6);

            $view = View::make('shows.ajax.items', compact(['shows', 'lang']))->render();
            $snippets = ['snippet-items' => $view];
            print json_encode(['snippets' => $snippets]);
            exit();
        }

        return view('shows.listing', compact(['filter', 'shows', 'lang']));
    }

    public function detailTranslate($lang, $slug) {

        $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                ->where('slug', '=', $slug)
                ->where('lang', '=', $lang)
                ->select('shows.*')// just to avoid fetching anything from joined table
                ->first();


        if (Utils::isAjax()) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");

            if ($_GET['season'] > 0) {
                $season = $show->seasonEpisodes($_GET['season']);
                $view = View::make('shows.ajax.season', compact(['season', 'lang']))->render();
                $snippets = ['snippet-season-' . $_GET['season'] => $view];
                print json_encode(['snippets' => $snippets]);
                exit();
            }
        }

        return view('shows.detail', compact(['show', 'lang']));
    }

    //TODO COPY
    public function detail($slug) {
        $lang = "cs";
        $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                ->where('slug', '=', $slug)
                ->where('lang', '=', $lang)
                ->select('shows.*')// just to avoid fetching anything from joined table
                ->first();
        return view('shows.detail', compact(['show', 'lang']));
    }

    public function import() {

        ini_set('max_execution_time', 60 * 60 * 2);
        //$langs_xml = file_get_contents("http://thetvdb.com/api/5EA3012696C40059/languages.xml");

        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl' /* 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'he', 'ja', 'pt', 'zh', 'sl', 'hr', 'ko', 'sv', 'no' */
        ];

        $shows = [71663, 153021, 73871, 81189, 121361, 275274, 248736, 79168, 80379, 75682];
        foreach ($shows as $id) {

            $enTitle = null;
            $enContent = null;
            $showId = null;

            foreach ($langs as $lang) {

                //$filename = "public/xml/" . $id . "-" . $lang . ".xml";
                $xml = simplexml_load_string(file_get_contents("http://thetvdb.com/api/5EA3012696C40059/series/$id/all/$lang.xml"));
                //echo "<pre>";print_r($xml);die;
                //dd($xml->Episode);
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

                    //Images
                    $fanart = trim($xml->Series->fanart);
                    if (!empty($fanart)) {
                        $fanart = 'http://thetvdb.com/banners/' . $fanart;
                        $arr = ['type' => 'fanart', 'extension' => 'jpg', 'external_patch' => $fanart, 'model_id' => $showId, 'model_type' => 'App\Show'];
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

}
