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

        $episode = Episode::join('episodes_translations as translation', 'translation.episode_id', '=', 'episodes.id')
                ->where('translation.slug', '=', $slugEpisode)
                ->where('translation.lang', '=', $lang)
                ->select('episodes.*')// just to avoid fetching anything from joined table
                ->first();

        return view('episodes.detail', compact(['show', 'episode']));
    }

    public function detailTranslate($lang, $slugShow, $slugEpisode) {
        
    }

}
