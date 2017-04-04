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
use App\Page;
use App\PageTranslation;
use Validator;
use Redirect;
use Cookie;
use DB;
use App\Functions\Utils;
use Lang;

class PagesController extends Controller {

    public function homepage($lang = 'cs', $slug) {
        $shows = Show::paginate(5);
        return view('shows.listing', compact(['shows', 'lang']));
    }

    public function page($lang = 'en', $slug) {
        $shows = Show::paginate(5);
        return view('shows.listing', compact(['shows', 'lang']));
    }

    public function shows($lang = 'en', $slug) {
        $shows = Show::paginate(5);
        return view('shows.listing', compact(['shows', 'lang']));
    }

    public function showsDetail($lang = 'en', $slug) {
        $shows = Show::paginate(5);
        return view('shows.listing', compact(['shows', 'lang']));
    }

}
