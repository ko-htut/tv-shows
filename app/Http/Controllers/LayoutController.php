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

class LayoutController extends Controller {

    public $layout;

    public function __construct() {

        $this->layout = [
            'lang' => isset(Route::current()->parameters['lang']) ? Route::current()->parameters['lang'] : 'cs',
            'lang_prefix' => isset(Route::current()->parameters['lang']) ? '/'.Route::current()->parameters['lang'] : '',
            'langs' => Language::all(),
            'genres' => Term::join('terms_translations as translation', 'translation.term_id', '=', 'terms.id')->select('terms.*')->distinct('terms.id')->orderBy('translation.title', 'ASC')->get(),
            'genres_counter' => DB::table('terms_to_models')->select(DB::raw('term_id, count(*) as count'))->groupBy('term_id')->get()->keyBy('term_id')->toArray(),
            'networks' => \App\Option::where('select_id', '=', Select::where('title', '=', 'network')->first()->id)->get(),
            'options_counter' => DB::table('options_to_models')->select(DB::raw('option_id, count(*) as count'))->groupBy('option_id')->get()->keyBy('option_id')->toArray(),
        ];
        View::share('layout', $this->layout);
       
    }

}
