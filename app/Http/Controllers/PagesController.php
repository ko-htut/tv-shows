<?php

namespace App\Http\Controllers;

use App\Episode;


class PagesController extends LayoutController {

    public function calendar() {
        

        $start = date('Y-m-d', strToTime('-3 days'));
        $end = date('Y-m-d', strToTime('+3 days'));
        
        $episodes = Episode::whereBetween('first_aired', [$start, $end])->orderBy('first_aired')->get();
        
        dd($episodes);
    }

}
