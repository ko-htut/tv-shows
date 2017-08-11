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
use App\Option;
use App\Select;
use App\Actor;
use Cocur\Slugify\Slugify;

class ActorsController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function index($lang = DEF_LANG) {

        $actors = new Actor;
        $actors = $actors->distinct('slug')->orderBy('sort', 'asc');
        $results = $actors->count('slug');
        $actors = $actors->paginate(4 * 3 * 5);
        
        if(isset($_GET['update'])){
            $this->updateSlugs();
        }

        return view('actors.index', compact(['actors']));
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

        $actors = Actor::where('slug', $slug)->get();
        
        $actor = $actors[0];
        $ids = [];
        foreach ($actors as $a) {
            $ids[] = $a->id;
        }

        $shows = Show::join('actors_to_models as atm', 'atm.model_id', '=', 'shows.id')
                ->whereIn('atm.actor_id', $ids)
                ->select('shows.*')
                ->distinct('shows.id')
                ->get();


        return view('actors.detail', compact(['actor', 'actors', 'shows']));
    }


    public function updateSlugs() {

        $actors = \App\Actor::all();
        foreach ($actors as $item) {
            $item = $item->setSlug();
        }
    }

}
