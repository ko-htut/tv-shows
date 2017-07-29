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

class TermsController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function index($lang = DEF_LANG) {
        $terms = Term::join('terms_translations as translation', 'translation.term_id', '=', 'terms.id')->select('terms.*')->distinct('terms.id')->orderBy('translation.title', 'ASC')->get();
        return view('terms.index', compact(['terms']));
    }

    public function detail($slug) {


        $lang = 'en';
        $limit = 2 * 3 * 5;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $next_page = $page + 1;

        $term = Term::join('terms_translations as translation', 'translation.term_id', '=', 'terms.id')
                ->where('translation.slug', '=', $slug)
                ->where('translation.lang', '=', $lang)
                ->select('terms.*')// just to avoid fetching anything from joined table
                ->first();

        $shows = new Show;
        $shows = $shows->join('terms_to_models as ttm', 'ttm.model_id', '=', 'shows.id')
                        ->where('ttm.term_id', $term->id)
                        ->select('shows.*')->distinct('shows.id');
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

        return view('terms.detail', compact(['term', 'shows', 'page', 'next_page', 'results', 'more']));
    }

    public function detailTranslate($lang, $slug) {
        $lang = 'en';
        $limit = 2 * 3 * 5;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $next_page = $page + 1;

        $term = Term::join('terms_translations as translation', 'translation.term_id', '=', 'terms.id')
                ->where('translation.slug', '=', $slug)
                ->where('translation.lang', '=', $lang)
                ->select('terms.*')// just to avoid fetching anything from joined table
                ->first();

        $shows = new Show;
        $shows = $shows->join('terms_to_models as ttm', 'ttm.model_id', '=', 'shows.id')
                        ->where('ttm.term_id', $term->id)
                        ->select('shows.*')->distinct('shows.id');
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

        return view('terms.detail', compact(['term', 'shows', 'page', 'next_page', 'results', 'more']));
    }
    
}
