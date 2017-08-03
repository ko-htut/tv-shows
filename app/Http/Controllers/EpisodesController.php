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
use App\Select;

class EpisodesController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function detail($slugShow, $slugEpisode) {

        $lang = DEF_LANG;
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
        $episode = Episode::where('show_id', '=', $show->id)->where('season_number', '=', $seasonNumber)->where('episode_number', '=', $episodeNumber)->first();
        return view('episodes.detail', compact(['show', 'episode']));
    }

    public function detailTranslate($lang, $slugShow, $slugEpisode) {

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
        $episode = Episode::where('show_id', '=', $show->id)->where('season_number', '=', $seasonNumber)->where('episode_number', '=', $episodeNumber)->first();
        return view('episodes.detail', compact(['show', 'episode']));
    }

}
