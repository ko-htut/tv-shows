<?php

namespace App\Http\Controllers;

use App\Episode;


class PagesController extends LayoutController {

    public function calendar() {
        

        $start = date('Y-m-d', strToTime('-1 days'));
        $end = date('Y-m-d', strToTime('+3 days'));
        $episodes = Episode::whereBetween('first_aired', [$start, $end])->distinct()->orderBy('first_aired')->get();
        
        return view('pages.calendar.calendar', compact(['episodes']));
       
    }

}
