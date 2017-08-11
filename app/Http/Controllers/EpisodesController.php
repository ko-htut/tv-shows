<?php

namespace App\Http\Controllers;

use App\Functions\Utils;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use View;
use Image;
use App\Language;
use App\Term;
use App\Show;
use App\Episode;
use App\Option;
use App\UserPivot;

class EpisodesController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function detail($first = null, $second = null, $third = null) {
        //public function detail($slugShow, $slugEpisode) {
        //public function detailTranslate($lang, $slugShow, $slugEpisode) {

        $lang = null;
        $slugShow = null;
        $slugEpisode = null;

        if (!empty($third)) {
            //translatins 
            $lang = $first;
            $slugShow = $second;
            $slugEpisode = $third;
        } else {
            $lang = DEF_LANG;
            $slugShow = $first;
            $slugEpisode = $second;
        }


        if (!is_numeric($slugShow)) {
            $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                    ->where('slug', '=', $slugShow)
                    ->where('lang', '=', $lang)
                    ->select('shows.*')// just to avoid fetching anything from joined table
                    ->first();


            if (!$show) {
                $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                        ->where('slug', '=', $slugShow)
                        ->select('shows.*')
                        ->firstOrFail();
            }
        } else {
            $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                    ->where('shows.id', '=', $slugShow)
                    ->where('lang', '=', $lang)
                    ->select('shows.*')// just to avoid fetching anything from joined table
                    ->first();

            if (!$show) {
                $show = Show::join('shows_translations as translation', 'translation.show_id', '=', 'shows.id')
                        ->where('shows.id', '=', $slugShow)
                        ->select('shows.*')
                        ->firstOrFail();
            }
        }

        preg_match_all('!\d+!', $slugEpisode, $numbers);
        $seasonNumber = isset($numbers[0][0]) ? intval($numbers[0][0]) : 0;
        $episodeNumber = isset($numbers[0][1]) ? intval($numbers[0][1]) : 0;
        $episode = Episode::where('show_id', '=', $show->id)->where('season_number', '=', $seasonNumber)->where('episode_number', '=', $episodeNumber)->orderBy('id', 'DESC')->first();

        //Users actions
        $isWatched = false;
        if (\Auth::user()) {
            $arr = [
                'user_id' => \Auth::user()->id,
                'model_id' => $episode->id,
                'model_type' => $episode->type,
                'action' => 'watched',
            ];
            $isWatched = UserPivot::where($arr)->first() ? true : false;
        }

        if (Utils::isAjax()) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            //Actions 
            if (isset($_GET['watched']) && $_GET['watched'] == true) {
                if (\Auth::user()) {
                    $arr = [
                        'user_id' => \Auth::user()->id,
                        'model_id' => $episode->id,
                        'model_type' => $episode->type,
                        'action' => 'watched',
                    ];
                    $isWatched = false;
                    $pivot = UserPivot::where($arr)->first();
                    if ($pivot) {
                        $pivot->delete();
                        $isWatched = false;
                    } else {
                        UserPivot::create($arr);
                        $isWatched = true;
                    }
                    $view = View::make('episodes.ajax.watched', compact(['isWatched']))->render();
                    $snippets = ['snippet-watched' => $view];
                    print json_encode(['snippets' => $snippets]);
                    exit();
                }
                exit();
            }
        }

        return view('episodes.detail', compact(['show', 'episode', 'isWatched']));
    }

}
