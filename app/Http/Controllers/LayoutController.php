<?php

namespace App\Http\Controllers;

use View;
use App\Language;
use Illuminate\Support\Facades\Route;

class LayoutController extends Controller {

    protected $layout;

    public function __construct() {
        $this->layout = [
            'lang' => isset(Route::current()->parameters['lang']) ? Route::current()->parameters['lang'] : DEF_LANG,
            'langs' => Language::all(),
        ];
        View::share('layout', $this->layout);
    }

}
