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

class NetworksController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function index($lang = DEF_LANG) {
        
    }

    public function detail($slug) {

        $lang = 'en';
        $limit = 2 * 3 * 5;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $next_page = $page + 1;

        $network = Option::join('options_translations as translation', 'translation.option_id', '=', 'options.id')
                ->where('translation.slug', '=', $slug)
                //->where('translation.option_id', '=', $id)
                ->where('translation.lang', '=', $lang)
                ->select('options.*')// just to avoid fetching anything from joined table
                ->first();

        //dd($network);

        $shows = new Show;

        $optionsKeys = ['network'];
        $options = [];
        foreach ($optionsKeys as $key) {
            $shows = $shows->join("options_to_models as $key", "$key.model_id", "=", "shows.id");
            $options = [];
            foreach ([$network->id] as $value) {
                $options[] = $value;
            }
            $shows = $shows->whereIn("$key.option_id", $options);
        }

        $shows = $shows->select('shows.*')->distinct('shows.id');
        $results = $shows->count('shows.id'); //'shows.id'
        $shows = $shows->paginate($limit);
        $more = ceil($results / $limit) > $page ? true : false;

        if (Utils::isAjax()) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");

            $view = View::make('shows.ajax.items', compact(['shows', 'page', 'next_page', 'more', 'results']))->render();
            $snippets = [$page == 1 ? 'snippet-wrapper' : 'snippet-more' => $view];
            print json_encode(['snippets' => $snippets]);
            exit();
        }

        return view('networks.detail', compact([ 'network', 'shows', 'page', 'next_page', 'results', 'more']));
    }
    
    public function detailTranslate($lang, $slug) {

        $lang = 'en';
        $limit = 2 * 3 * 5;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $next_page = $page + 1;

        $network = Option::join('options_translations as translation', 'translation.option_id', '=', 'options.id')
                ->where('translation.slug', '=', $slug)
                //->where('translation.option_id', '=', $id)
                ->where('translation.lang', '=', $lang)
                ->select('options.*')// just to avoid fetching anything from joined table
                ->first();

        //dd($network);

        $shows = new Show;

        $optionsKeys = ['network'];
        $options = [];
        foreach ($optionsKeys as $key) {
            $shows = $shows->join("options_to_models as $key", "$key.model_id", "=", "shows.id");
            $options = [];
            foreach ([$network->id] as $value) {
                $options[] = $value;
            }
            $shows = $shows->whereIn("$key.option_id", $options);
        }

        $shows = $shows->select('shows.*')->distinct('shows.id');
        $results = $shows->count('shows.id'); //'shows.id'
        $shows = $shows->paginate($limit);
        $more = ceil($results / $limit) > $page ? true : false;

        if (Utils::isAjax()) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");

            $view = View::make('shows.ajax.items', compact(['shows', 'page', 'next_page', 'more', 'results']))->render();
            $snippets = [$page == 1 ? 'snippet-wrapper' : 'snippet-more' => $view];
            print json_encode(['snippets' => $snippets]);
            exit();
        }

        return view('networks.detail', compact([ 'network', 'shows', 'page', 'next_page', 'results', 'more']));
    }


}
