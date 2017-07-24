<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Functions\Utils;
use App\Android\Api;



define('DEFAULT_LANGUAGE', 'en');
define('DEFAULT_LIMIT', 20);
define('FIRST_PAGE', 0);
define('MODEL_SHOW', 'App\Show');
define('MODEL_EPISODE', 'App\Episode');
define('MODEL_ACTOR', 'App\Actor');
define('MODEL_USER', 'App\User');


class AndroidApiController extends Controller {

    public function index(Request $request) {
       
        
        
        header("Content-type:application/json");
        if (isset($_POST['action'])) {
            $a = new Api();
            switch ($_POST['action']) {
                case 'SHOWS_LIST':
                    $a->getShowsList();
                    exit();
                case 'FAVOURITES_LIST':
                    $a->getFavouritesList();
                    exit();
                case 'GENRES_LIST':
                    $a->getGenresList();
                    exit();
                case 'NETWORKS_LIST':
                    $a->getNetworksList();
                    exit();
                case 'SHOW_DETAIL':
                    $a->getShowDetail();
                    exit();
                case 'EPISODE_DETAIL':
                    $a->getEpisodeDetail();
                    exit();
                case 'SEASON_LIST':
                    $a->getSeasonList();
                    exit();
                case 'CALENDAR_LIST':
                    $a->getCalendarList();
                    exit();
                case 'USER_REGISTRATION':
                    $a->userRegistration();
                    exit();
                case 'USER_LOGIN':
                    $a->userLogin();
                    exit();
                case 'USER_FB_LOGIN':
                    $a->facebookLogin();
                    exit();
                case 'USER_TO_SHOW':
                    $a->userToShow();
                    exit();

                case 'USER_TO_EPISODE':
                    $a->userToEpisode();
                    exit();

                case 'USER_RATE':
                    $a->userRate();
                    exit();
                case 'USER_NOTIFICATIONS':
                    $a->userNotification();
                    exit();
                case 'USER_UPDATE':
                    $a->userUpdate();
                    exit();
            }
        }
        die("Api");
    }

}
