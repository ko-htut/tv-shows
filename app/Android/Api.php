<?php

namespace App\Android;

try {
	\dibi::connect([
		'driver' => 'mysqli',
		'host' => DB_HOST,
		'username' => DB_USERNAME,
		'password' => DB_PASSWORD,
		'database' => DB_DATABASE,
		'options' => [
			MYSQLI_OPT_CONNECT_TIMEOUT => 30,
		],
		'flags' => MYSQLI_CLIENT_COMPRESS,
	]);
} catch (Dibi\Exception $e) {
	echo get_class($e), ': ', $e->getMessage(), "\n";
}


date_default_timezone_set('Europe/Prague');



class Api {

    function __construct() {
        
    }

    public function displayPosters() {

        echo "<style>body{padding:0;margin:0px}</style>";
        $shows = [
            2, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 24, 26, 27, 28, 30, 32, 34, 35, 36, 37, 39, 41,
            42, 43, 47, 48, 50, 51, 54, 55, 56, 57, 58, 59, 60, 64, 72, 74, 78, 80, 81, 82, 83, 86, 87, 88, 89,
            93, 94, 95, 97, 98, 103, 107, 108, 110, 111, 112, 115, 121, 126
        ];
        $posters = \dibi::select('external_patch')
                ->from('files')
                ->where('type = ?', 'poster')
                ->where('model_id IN %in', $shows)
                ->where('model_type = ?', MODEL_SHOW)
                ->orderBy('RAND()')
                ->limit(20)
                ->fetchAll();

        $i = 0;
        foreach ($posters as $poster) {
            echo "<img src={$poster->external_patch} width='203' >";
            $i++;
            if ($i % 4 == 0) {
                echo "<br>";
            }
        }
    }

    public function resizePosters() {
        
        ini_set('max_execution_time', 60 * 60 * 10);

        $posters = \dibi::select('id, external_patch, model_id, type')
                ->from('files')
                ->where('type = ?', 'poster')
                //->where('model_type = ?', MODEL_SHOW)
                ->where('patch IS NULL')
                
                ->orderBy('model_id', 'DESC')
                ->fetchAll();
        
        

        require_once __DIR__ . '/vendor/shekarsiri/simpleimage/src/abeautifulsite/SimpleImage.php';

        
        
        foreach ($posters as $poster) {

            $poster->id;
            $poster->external_patch;
            $poster->model_id;
            $poster->type;
            
            if(preg_match_all('/\d+/', $poster->external_patch, $numbers)){
                $lastnum = end($numbers[0]);
            }

            $patch = 'files/shows/' . $poster->model_id . '-' . $poster->type . '-' . $lastnum . '.jpg';

            
            try {
                file_put_contents($patch, file_get_contents($poster->external_patch));
                $image = new abeautifulsite\SimpleImage($patch);
                $image->fit_to_width(120)->save($patch);
                $args = [
                    'patch' => $patch,
                ];
                \dibi::update('files', $args)->where('id = ?', $poster->id)->execute();
            } catch (Exception $e) {
                die($e);
            }
        }
    }

    public function getShowsList() {

        $lang = isset($_POST['lang']) ? $_POST['lang'] : DEFAULT_LANGUAGE;
        $page = isset($_POST['page']) ? $_POST['page'] : FIRST_PAGE;
        $page = $page + 1; //staring at 0;
        $limit = DEFAULT_LIMIT;
        $offset = $page * $limit - $limit;
        $data = [];

        $searchQuery = isset($_POST['searchQuery']) && !empty($_POST['searchQuery']) ? $_POST['searchQuery'] : null;
        $genreId = isset($_POST['genreId']) && !empty($_POST['genreId']) ? $_POST['genreId'] : null;
        $networkId = isset($_POST['networkId']) && !empty($_POST['networkId']) ? $_POST['networkId'] : null;

        $showsQuery = \dibi::select('show_id, title, rating, first_aired')
                        ->from('shows')
                        ->join('shows_translations as translation')->on('shows.id = translation.show_id');

        if ($searchQuery) {
            $showsQuery->where('translation.title LIKE %~like~', $searchQuery);
        }

        if ($genreId) {
            $showsQuery->join('terms_to_models as ttm')->on('ttm.model_id = shows.id')
                    ->where('ttm.term_id = ?', $genreId);
        }

        if ($networkId) {
            $showsQuery->join('options_to_models as opm')->on('opm.model_id = shows.id')
                    ->where('opm.option_id = ?', $networkId);
        }

        $showsQuery->where('translation.lang = ?', $lang)
                ->where('shows.active = ?', true)
                ->offset($offset)
                ->limit($limit)
                ->orderBy('rating_count DESC');

        $shows = $showsQuery
                ->execute()
                ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                ->fetchAssoc('show_id');

        $showsId = array_keys($shows);

        $images = \dibi::select('model_id, files.id as file_id, external_patch, patch, type')
                ->from('files')
                ->where('type = ?', 'poster')
                ->where('model_type = ?', MODEL_SHOW)
                ->where('model_id IN %in', $showsId)
                ->where('files.active = ?', true)
                ->orderBy('sort', 'DESC')
                ->fetchAssoc('model_id,type'); //

        $translations = \dibi::select('show_id, title')
                ->from('shows_translations')
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->where('show_id IN %in', $showsId)
                ->fetchAssoc('show_id'); //


        if ($page == 1) {
            $genre = null;
            if ($genreId) {
                $genre = \dibi::select('terms.id as genre_id, title')
                        ->from('terms')
                        ->join('terms_translations as translation')->on('terms.id = translation.term_id')
                        ->where('translation.lang = ?', DEFAULT_LANGUAGE)
                        ->where('terms.id = ?', $genreId)
                        ->where('terms.active = ?', true)
                        ->fetch();
            }

            $network = null;
            if ($networkId) {
                $network = \dibi::select('options.id as network_id, value')
                        ->from('options')
                        ->join('options_translations as translation')->on('options.id = translation.option_id')
                        ->where('select_id = ?', \dibi::select('id')->from('selects')->where('title = ?', 'network')->fetchSingle())//select the id of the select
                        ->where('translation.lang = ?', DEFAULT_LANGUAGE)
                        ->where('options.id = ?', $networkId)
                        ->where('options.active = ?', true)
                        ->fetch();
            }
            if ($genre) {
                $data['genre'] = $genre;
            }
            if ($network) {
                $data['network'] = $network;
            }
        }


        foreach ($shows as $show) {
            $id = $show['show_id'];
            $show['images'] = $images[$id];
            $item = [
                'show' => $show,
            ];

            $item['show']['title'] = !$item['show']['title'] ? $translations[$id]['title'] : $item['show']['title'];

            $data['shows'][$id] = $item;
        }

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getFavouritesList() {

        $lang = isset($_POST['lang']) ? $_POST['lang'] : DEFAULT_LANGUAGE;
        $userId = $_POST['userId'];
        $page = isset($_POST['page']) ? $_POST['page'] + 1 : FIRST_PAGE + 1;
        $limit = DEFAULT_LIMIT;
        $offset = $page * $limit - $limit;
        $data = [];
        $shows = [];

        $count = \dibi::select('COUNT(model_id)')
                ->from('users_to_models')
                ->where('user_id = ?', $userId)
                ->where('model_type = ?', MODEL_SHOW)
                ->where('action = ?', 'follow')
                ->fetchSingle();


        if ($count > 0) {
            $showsId = \dibi::select('model_id')
                    ->from('users_to_models')
                    ->where('user_id = ?', $userId)
                    ->where('model_type = ?', MODEL_SHOW)
                    ->where('action = ?', 'follow')
                    ->fetchPairs();

            $shows = \dibi::select('show_id, title, rating, first_aired')
                    ->from('shows')
                    ->join('shows_translations as translation')->on('shows.id = translation.show_id')
                    ->where('show_id IN %in', $showsId)
                    ->where('translation.lang = ?', $lang)
                    ->where('shows.active = ?', true)
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy('rating DESC')
                    ->execute()
                    ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                    ->fetchAssoc('show_id');

            $images = \dibi::select('model_id, files.id as file_id, external_patch, patch, type')
                    ->from('files')
                    ->where('type = ?', 'poster')
                    ->where('model_type = ?', MODEL_SHOW)
                    ->where('model_id IN %in', $showsId)
                    ->where('files.active = ?', true)
                    ->fetchAssoc('model_id,type'); //,# if multiple

            $translations = \dibi::select('show_id, title')
                    ->from('shows_translations')
                    ->where('lang = ?', DEFAULT_LANGUAGE)
                    ->where('show_id IN %in', $showsId)
                    ->fetchAssoc('show_id'); //
        }


        foreach ($shows as $show) {
            $id = $show['show_id'];
            $show['images'] = $images[$id];
            $item = [
                'show' => $show,
            ];

            $item['show']['title'] = !$item['show']['title'] ? $translations[$id]['title'] : $item['show']['title'];


            $data['shows'][$id] = $item;
        }
        $data['favourites_count'] = $count;

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getGenresList() {

        $genres = \dibi::select('terms.id as genre_id, title, COUNT(terms.id) AS shows_count')
                ->from('terms')
                ->join('terms_translations as translation')->on('terms.id = translation.term_id')
                ->join('terms_to_models as pivot')->on('terms.id = pivot.term_id')
                ->where('model_type = ?', MODEL_SHOW)
                ->where('translation.lang = ?', DEFAULT_LANGUAGE)//TODO
                ->where('terms.active = ?', true)
                ->groupBy('terms.id')
                ->orderBy('title')
                ->fetchAll();

        echo json_encode($genres, JSON_PRETTY_PRINT);
    }

    public function getNetworksList($title = 'network') {

        $networks = \dibi::select('options.id as network_id, value, COUNT(options.id) AS shows_count')
                ->from('options')
                ->join('options_translations as translation')->on('options.id = translation.option_id')
                ->join('options_to_models as pivot')->on('options.id = pivot.option_id')
                ->where('select_id = ?', \dibi::select('id')->from('selects')->where('title = ?', $title)->fetchSingle())//select the id of the select
                ->where('model_type = ?', MODEL_SHOW)
                ->where('translation.lang = ?', DEFAULT_LANGUAGE)
                ->where('options.active = ?', true)
                ->groupBy('options.id')
                ->orderBy('shows_count', 'DESC')
                ->fetchAll();

        echo json_encode($networks, JSON_PRETTY_PRINT);
    }

    public function getSeasonList() {

        $showId = $_POST['showId'];
        $sesonNumber = $_POST['seasonNumber'];
        $lang = $_POST['lang'];

        $show = \dibi::select('show_id, title')
                ->from('shows')
                ->join('shows_translations as translation')->on('shows.id = translation.show_id')
                ->where('show_id = ?', $showId)
                ->where('translation.lang = ?', $lang)
                ->where('shows.active = ?', true)
                ->fetch();

        $episodes = \dibi::select('episode_id, title, season_number, episode_number, first_aired')
                ->from('episodes')
                ->join('episodes_translations as translation')->on('episodes.id = translation.episode_id')
                ->where('show_id = ?', $showId)
                ->where('season_number = ?', $sesonNumber)
                ->where('translation.lang = ?', $lang)
                ->execute()
                ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                ->fetchAll();

        $episodesIds = [];
        foreach ($episodes as $e) {
            $episodesIds[] = $e['episode_id'];
        }

        $seasons = \dibi::select('season_number as season, COUNT(season_number) as episodes')
                ->from('episodes')
                ->where('show_id = ?', $showId)
                ->where('season_number IS NOT NULL')
                ->groupBy('show_id, season_number')
                ->fetchAll();


        $translations = dibi::select('episode_id, title')
                ->from('episodes_translations')
                ->where('episode_id IN %in', $episodesIds)
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->fetchAssoc('episode_id');


        foreach ($episodes as $episode) {
            $episode['title'] = !$episode['title'] ? $translations[$episode['episode_id']]['title'] : $episode['title'];
        }

        $translationsShow = \dibi::select('title')
                ->from('shows_translations')
                ->where('show_id = ?', $showId)
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->fetch();
        $show['title'] = !$show['title'] ? $translationsShow['title'] : $show['title'];



        $show['episodes'] = $episodes;
        $show['seasons'] = $seasons;


        $data = [
            'show' => $show,
        ];

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getShowDetail() {

        $showId = $_POST['showId'];
        $lang = $_POST['lang'];
        $userId = $_POST['userId'];

        $show = \dibi::select('show_id, title, content, lang, runtime, thetvdb_id, imdb_id, first_aired, air_day, air_time, ended, rating,rating_count')
                ->from('shows')
                ->join('shows_translations as translation')->on('shows.id = translation.show_id')
                ->where('show_id = ?', $showId)
                ->where('translation.lang = ?', $lang)
                ->where('shows.active = ?', true)
                ->execute()
                ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                ->fetch();

        $images = \dibi::select('id as file_id, external_patch, patch, type, sort')
                ->from('files')
                ->where('model_type = ?', MODEL_SHOW)
                ->where('type = ?', 'fanart')
                ->where('model_id = ?', $showId)
                ->where('files.active = ?', true)
                ->orderBy('sort DESC')
                ->fetchAssoc('type,#'); //,# if multiple

        $options = \dibi::select('title, value, options.id as option_id')
                ->from('options')
                ->join('selects')->on('selects.id = options.select_id')
                ->join('options_translations as translation')->on('options.id = translation.option_id')
                ->join('options_to_models as pivot')->on('options.id = pivot.option_id')
                ->where('model_type = ?', MODEL_SHOW)
                ->where('model_id = ?', $showId)
                ->where('options.active = ?', true)
                ->fetchAssoc('title'); //,# if multiple only one here

        $seasons = \dibi::select('season_number as season, COUNT(season_number) as episodes')
                ->from('episodes')
                ->where('show_id = ?', $showId)
                ->where('season_number IS NOT NULL')
                ->groupBy('show_id, season_number')
                ->orderBy('season DESC')
                ->fetchAll();

        $genres = \dibi::select('terms.id as term_id, title')
                ->from('terms')
                ->join('terms_translations as translation')->on('terms.id = translation.term_id')
                ->join('terms_to_models as pivot')->on('terms.id = pivot.term_id')
                ->where('model_id = ?', $showId)
                ->where('model_type = ?', MODEL_SHOW)
                ->where('terms.active = ?', true)
                ->fetchAll();

        $actors = \dibi::select('actor_id, name, role, files.external_patch as image')
                ->from('actors')
                ->join('actors_to_models as pivot')->on('actors.id = pivot.actor_id')
                ->join('files')->on('actors.id = files.model_id')
                ->where('pivot.model_id = ?', $showId)
                ->where('pivot.model_type = ?', MODEL_SHOW)
                ->where('files.model_type = ?', MODEL_ACTOR)
                //->where('actors.sort < ?', 3)
                ->orderBy('actors.sort ASC')
                ->where('actors.active = ?', true)
                ->limit(15)
                ->fetchAll();


        $translation = \dibi::select('title, content')
                ->from('shows_translations')
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->where('show_id = ?', $showId)
                ->fetch();

        $show['title'] = !$show['title'] ? $translation['title'] : $show['title'];
        $show['content'] = !$show['content'] ? $translation['content'] : $show['content'];

        $show['actors'] = $actors;
        $show['images'] = $images;
        $show['options'] = $options;
        $show['seasons'] = $seasons;
        $show['genres'] = $genres;


//User
        $user_actions = \dibi::select('action, created_at')
                ->from('users_to_models')
                ->where('user_id = ?', $userId)
                ->where('model_id = ?', $showId)
                ->where('model_type= ?', MODEL_SHOW)
                ->execute()
                ->setFormat(\dibi::FIELD_DATETIME, 'Y-m-d')
                ->fetchPairs();

        $data = [
            'show' => $show,
            'user_actions' => $user_actions,
        ];

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getEpisodeDetail() {

        $showId = $_POST['showId'];
        $episodeId = $_POST['episodeId'];
        $lang = $_POST['lang'];
        $userId = $_POST['userId'];

        $show = \dibi::select('show_id, title, lang')
                ->from('shows')
                ->join('shows_translations as translation')->on('shows.id = translation.show_id')
                ->where('show_id = ?', $showId)
                ->where('translation.lang = ?', $lang)
                ->where('shows.active = ?', true)
                ->fetch();


        $episode = \dibi::select('episode_id, lang, title, content, thetvdb_id, imdb_id, first_aired, season_number, episode_number, rating, rating_count, last_updated, show_id')
                ->from('episodes')
                ->join('episodes_translations as translation')->on('episodes.id = translation.episode_id')
                ->where('show_id = ?', $showId)
                ->where('episode_id = ?', $episodeId)
                ->where('lang = ?', $lang)
                ->execute()
                ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                ->fetch();

        $imagesEpisode = \dibi::select('id as file_id, external_patch, patch, type')
                ->from('files')
                ->where('model_type = ?', MODEL_EPISODE)
                ->where('model_id = ?', $episodeId)
                ->where('files.active = ?', true)
                ->fetchAssoc('type'); //,# if multiple

        $episode['images'] = $imagesEpisode;

        $translation = \dibi::select('title, content')
                ->from('episodes_translations')
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->where('episode_id = ?', $episodeId)
                ->fetch();

        $episode['title'] = !$episode['title'] ? $translation['title'] : $episode['title'];
        $episode['content'] = !$episode['content'] ? $translation['content'] : $episode['content'];

        $translationShow = \dibi::select('title')
                ->from('shows_translations')
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->where('show_id = ?', $showId)
                ->fetch();

        $show['title'] = !$show['title'] ? $translationShow['title'] : $show['title'];


//User
        $user_actions = \dibi::select('action, created_at')
                ->from('users_to_models')
                ->where('user_id = ?', $userId)
                ->where('model_id = ?', $episodeId)
                ->where('model_type= ?', MODEL_EPISODE)
                ->execute()
                ->setFormat(\dibi::FIELD_DATETIME, 'Y-m-d')
                ->fetchPairs();

        $data = [
            'show' => $show,
            'episode' => $episode,
            'user_actions' => $user_actions
        ];

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getCalendarList() {

        $lang = $_POST['lang'];
        $userId = $_POST['userId'];

        $data = [];
        $showsIds = [];
        
        $lang = "cs";

        $episodes = \dibi::select('episode_id, lang, title, first_aired, season_number, episode_number, show_id')
                ->from('episodes')
                ->join('episodes_translations as translation')->on('episodes.id = translation.episode_id')
                ->where('translation.lang = ?', $lang)
                ->where('episodes.active = ?', true)
                ->where('first_aired BETWEEN ? AND ?', date("Y-m-d", strToTime('-2 weeks')), date("Y-m-d", strToTime('+2 weeks')))
                ->orderBy('first_aired')
                ->execute()
                ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                ->fetchAssoc('episode_id');
        
        
        $episodesIds = array_keys($episodes);
       
        
        $translations = \dibi::select('episode_id, title')
                ->from('episodes_translations')
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->where('episode_id IN %in', $episodesIds)
                ->fetchAssoc('episode_id');
        
        

        

        foreach ($episodes as $episode) {

            $id = $episode['episode_id'];
            $episode['title'] = !$episode['title'] ? $translations[$id]['title'] : $episode['title'];
            $item = [
                'episode' => $episode,
            ];
            $data['episodes'][$id] = $item;

            $showsIds[] = $episode['show_id'];
        }

        $showsIds = array_unique($showsIds);

        $shows = \dibi::select('show_id, title, lang')
                ->from('shows')
                ->join('shows_translations as translation')->on('shows.id = translation.show_id')
                ->where('show_id IN %in', $showsIds)
                ->where('translation.lang = ?', $lang)
                ->where('shows.active = ?', true)
                ->fetchAssoc('show_id');

        $showsImages = \dibi::select('id as file_id, model_id as show_id, external_patch, patch, type')
                ->from('files')
                ->where('model_type = ?', MODEL_SHOW)
                ->where('type = ?', 'poster')
                ->where('model_id IN %in', $showsIds)
                ->where('files.active = ?', true)
                ->fetchAssoc('show_id,type'); //,# if multiple
                //
        //TODO TRANSLATIONS
        $translations = \dibi::select('show_id, title')
                ->from('shows_translations')
                ->where('show_id IN %in', $showsIds)
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->fetchAssoc('show_id');



        foreach ($shows as $show) {
            $id = $show['show_id'];
            $show['images'] = $showsImages[$id];
            $show['title'] = !$show['title'] ? $translations[$id]['title'] : $show['title'];

            $item = [
                'show' => $show,
            ];
            $data['shows'][$id] = $item;
        }

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function userRegistration() {

        $error = array_filter([
            'email_invalid' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? false : true,
            'username_invalid' => mb_strlen($_POST['username']) >= 3 ? false : true,
            'password_invalid' => mb_strlen($_POST['password']) >= 4 ? false : true,
            'email_used' => \dibi::select('COUNT(email)')->from('users')->where('email = ?', $_POST['email'])->fetchSingle() == 0 ? false : true,
            'username_used' => \dibi::select('COUNT(username)')->from('users')->where('username = ?', $_POST['username'])->fetchSingle() == 0 ? false : true,
        ]);

        if (!$error) {
            $args = array_filter([
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => sha1($_POST['password']),
                'users_role_id' => 1, //Subsriber
            ]);

            try {
                \dibi::insert('users', $args)->execute();
                $userId = \dibi::insertId();
                $user = \dibi::select('*')
                        ->from('users')
                        ->where('id = ?', $userId)
                        ->execute()
                        ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                        ->fetch();

                $user['favourites_count'] = 0;


                echo json_encode(['succes' => true, 'user' => $user], JSON_PRETTY_PRINT);
            } catch (Dibi\Exception $e) {
                echo json_encode(['insert_user_error' => true], JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(['errors' => $error], JSON_PRETTY_PRINT);
        }
    }

    public function userLogin() {

        $username = $_POST['username'];
        $password = sha1($_POST['password']);

        $user = \dibi::select('*')
                ->from('users')
                ->where('(username = ? OR email = ?)', $username, $username)
                ->where('password = ?', $password)
                ->execute()
                ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                ->setFormat(\dibi::FIELD_DATETIME, 'Y-m-d H:i:s')
                ->fetch();

        $count = \dibi::select('COUNT(model_id)')
                ->from('users_to_models')
                ->where('user_id = ?', $user->id)
                ->where('model_type = ?', MODEL_SHOW)
                ->where('action = ?', 'follow')
                ->fetchSingle();
        $user['favourites_count'] = $count;

        if ($user) {
            echo json_encode(['succes' => true, 'user' => $user], JSON_PRETTY_PRINT);
        } else {
            $error = [
                'invalid_creditals' => true,
            ];
            echo json_encode(['errors' => $error], JSON_PRETTY_PRINT);
        }
    }

    public function facebookLogin() {

        $args = array_filter([
            'facebook_id' => $_POST['facebook_id'],
            'username' => $_POST['username'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'gender' => $_POST['gender'],
            //'birthday' => (validateDate($_POST['birthday'])) ? $_POST['birthday'] : null,
        ]);

        try {
            
            $update = \dibi::select('COUNT(facebook_id)')->from('users')->where('facebook_id = ?', $_POST['facebook_id'])->fetchSingle() == 0 ? false : true;

            if ($update) {
                
                //\dibi::update('users', $args)->where('facebook_id = ?', $args['facebook_id'])->execute();
                
                
            } else {
                \dibi::insert('users', $args)->execute();
            }

            $user = \dibi::select('*')
                    ->from('users')
                    ->where('facebook_id = ?', $_POST['facebook_id'])
                    ->execute()
                    ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                    ->fetch();

            $userImage = \dibi::select('*')
                        ->from('files')
                        ->where('model_id = ?', $_POST['userId'])
                        ->where('model_type = ?', MODEL_USER)
                        ->where('type = ?', 'profile')
                        ->execute()
                        ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                        ->fetch();
                
            $user['base64image'] = $userImage;


            echo json_encode(['succes' => true, 'user' => $user], JSON_PRETTY_PRINT);
        } catch (Dibi\Exception $e) {
            echo json_encode(['fb_insert_user_error' => $e], JSON_PRETTY_PRINT);
        }
    }

    public function userToShow() {

        $args = [
            'user_id' => $_POST['userId'],
            'model_id' => $_POST['showId'],
            'model_type' => MODEL_SHOW,
            'action' => $_POST['modelAction'], //follow
            'updated_at' => date("Y-m-d H:i:s", strToTime('now')),
            'created_at' => date("Y-m-d H:i:s", strToTime('now')),
        ];

        try {
            $delete = \dibi::select('COUNT(user_id)')
                            ->from('users_to_models')
                            ->where('user_id = ?', $_POST['userId'])
                            ->where('model_id = ?', $_POST['showId'])
                            ->where('model_type = ?', MODEL_SHOW)
                            ->where('action = ?', $_POST['modelAction'])
                            ->fetchSingle() == 0 ? false : true;

            if ($delete) {
                \dibi::delete('users_to_models')
                        ->where('user_id = ?', $_POST['userId'])
                        ->where('model_id = ?', $_POST['showId'])
                        ->where('model_type = ?', MODEL_SHOW)
                        ->where('action = ?', $_POST['modelAction'])
                        ->execute();
            } else {
                \dibi::insert('users_to_models', $args)->execute();
            }

            $pivot = \dibi::select('*')
                    ->from('users_to_models')
                    ->where('user_id = ?', $_POST['userId'])
                    ->where('model_id = ?', $_POST['showId'])
                    ->where('model_type = ?', MODEL_SHOW)
                    ->where('action = ?', $_POST['modelAction'])
                    ->execute()
                    ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                    ->fetch();



            $favourites_count = \dibi::select('COUNT(model_id)')
                    ->from('users_to_models')
                    ->where('user_id = ?', $_POST['userId'])
                    ->where('model_type = ?', MODEL_SHOW)
                    ->where('action = ?', 'follow')
                    ->fetchSingle();

            echo json_encode(array_filter(['succes' => true, 'pivot' => $pivot, 'favourites_count' => $favourites_count]), JSON_PRETTY_PRINT);
        } catch (Dibi\Exception $e) {
            echo json_encode(['follow_user_error' => true], JSON_PRETTY_PRINT);
        }
    }

    public function userToEpisode() {

        $args = [
            'user_id' => $_POST['userId'],
            'model_id' => $_POST['episodeId'],
            'model_type' => MODEL_EPISODE,
            'action' => $_POST['modelAction'],
            'updated_at' => date("Y-m-d H:i:s", strToTime('now')),
            'created_at' => date("Y-m-d H:i:s", strToTime('now')),
        ];

        try {
            $delete = \dibi::select('COUNT(user_id)')
                            ->from('users_to_models')
                            ->where('user_id = ?', $_POST['userId'])
                            ->where('model_id = ?', $_POST['episodeId'])
                            ->where('model_type = ?', MODEL_EPISODE)
                            ->where('action = ?', $_POST['modelAction'])
                            ->fetchSingle() == 0 ? false : true;

            if ($delete) {
                \dibi::delete('users_to_models')
                        ->where('user_id = ?', $_POST['userId'])
                        ->where('model_id = ?', $_POST['episodeId'])
                        ->where('model_type = ?', MODEL_EPISODE)
                        ->where('action = ?', $_POST['modelAction'])
                        ->execute();
            } else {
                \dibi::insert('users_to_models', $args)->execute();
            }

            $pivot = \dibi::select('*')
                    ->from('users_to_models')
                    ->where('user_id = ?', $_POST['userId'])
                    ->where('model_id = ?', $_POST['episodeId'])
                    ->where('model_type = ?', MODEL_EPISODE)
                    ->where('action = ?', $_POST['modelAction'])
                    ->execute()
                    ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                    ->fetch();

            echo json_encode(array_filter(['succes' => true, 'pivot' => $pivot]), JSON_PRETTY_PRINT);
        } catch (Dibi\Exception $e) {
            echo json_encode(['follow_user_error' => true], JSON_PRETTY_PRINT);
        }
    }

    public function userNotification() {

        $userId = $_POST['userId'];
        $lang = $_POST['lang'];

        $userShows = \dibi::select('model_id')
                ->from('users_to_models')
                ->where('user_id = ?', $userId)
                ->where('model_type = ?', MODEL_SHOW)
                ->where('action = ?', 'follow')
                ->fetchPairs();

        $episode = \dibi::select('episode_id, lang, title, first_aired, season_number, episode_number, show_id')
                ->from('episodes')
                ->join('episodes_translations as translation')->on('episodes.id = translation.episode_id')
                ->where('show_id IN %in', $userShows)
                ->where('first_aired BETWEEN ? AND ?', date("Y-m-d", strToTime('now')), date("Y-m-d", strToTime('+1 week')))
                ->where('translation.lang = ?', $lang)
                ->where('episodes.active = ?', true)
                ->execute()
                ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                ->fetch();

        $translationEpisode = \dibi::select('title')
                ->from('episodes_translations')
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->where('episode_id = ?', $episode->episode_id)
                ->fetch(); 
        $episode['title'] = !$episode['title'] ? $translationEpisode['title'] : $episode['title'];
        
        
        $show = \dibi::select('show_id, title')
                ->from('shows')
                ->join('shows_translations as translation')->on('shows.id = translation.show_id')
                ->where('show_id = ?', $episode->show_id)
                ->where('translation.lang = ?', $lang)
                ->where('shows.active = ?', true)
                ->fetch();
        
        $translationShow = \dibi::select('title')
                ->from('shows_translations')
                ->where('lang = ?', DEFAULT_LANGUAGE)
                ->where('show_id = ?', $episode->show_id)
                ->fetch();
          
        $show['title'] = !$show['title'] ? $translationShow['title'] : $show['title'];

        $data = [
            'show' => $show,
            'episode' => $episode,
        ];

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function userRate() {

        $args = [];

        $uId = $_POST['userId'];
        $mId = $_POST['modelId'];
        $mType = $_POST['modelType'];
        $value = $_POST['value'];


        $update = \dibi::select('COUNT(*)')->from('votes')->where('user_id = ?', $uId)->where('model_id = ?', $mId)->where('model_type = ?', $mType)->fetchSingle() == 0 ? false : true;

        if ($update) {

            $args = [
                'value' => $value,
                'updated_at' => date("Y-m-d H:i:s", strToTime('now')),
            ];

            \dibi::update('votes', $args)->where('user_id = ?', $uId)->where('model_id = ?', $mId)->where('model_type = ?', $mType)->execute();
        } else {

            $args = [
                'user_id' => $uId,
                'model_id' => $mId,
                'model_type' => $mType,
                'value' => $value,
                'updated_at' => date("Y-m-d H:i:s", strToTime('now')),
                'created_at' => date("Y-m-d H:i:s", strToTime('now')),
            ];

            \dibi::insert('votes', $args)->execute();
        }

        echo json_encode($args, JSON_PRETTY_PRINT);
    }

    public function userUpdate() {

        $error = array_filter([
            'email_invalid' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? false : true,
            'username_invalid' => mb_strlen($_POST['username']) >= 3 ? false : true,
            'email_used' => \dibi::select('COUNT(email)')->from('users')->where('id != ?', $_POST['userId'])->where('email = ?', $_POST['email'])->fetchSingle() == 0 ? false : true,
            'username_used' => \dibi::select('COUNT(username)')->from('users')->where('id != ?', $_POST['userId'])->where('username = ?', $_POST['username'])->fetchSingle() == 0 ? false : true,
        ]);

        if (!$error) {
            $args = array_filter([
                'lang' => $_POST['lang'],
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'about' => $_POST['about'],
                'gender' => $_POST['gender'],
                'birthday' => $_POST['birthday'],
                'updated_at' => date("Y-m-d H:i:s", strToTime('now'))
            ]);

            try {

                \dibi::update('users', $args)->where('id = ?', $_POST['userId'])->execute();

                $updateImage = \dibi::select('COUNT(*)')->from('files')
                                ->where('model_id = ?', $_POST['userId'])
                                ->where('model_type = ?', MODEL_USER)
                                ->where('type = ?', 'profile')
                                ->fetchSingle() == 0 ? false : true;

                if ($updateImage) {
                    \dibi::update('files', ['base64' => $_POST['image'], 'updated_at' => date("Y-m-d H:i:s", strToTime('now'))])
                            ->where('model_id = ?', $_POST['userId'])
                            ->where('model_type = ?', MODEL_USER)
                            ->where('type = ?', 'profile')->execute();
                } else {
                    \dibi::insert('files', [
                        'base64' => $_POST['image'],
                        'model_id' => $_POST['userId'],
                        'model_type' => MODEL_USER,
                        'type' => 'profile',
                        'updated_at' => date("Y-m-d H:i:s", strToTime('now')),
                        'created_at' => date("Y-m-d H:i:s", strToTime('now')),
                    ])->execute();
                }

                $user = \dibi::select('*')
                        ->from('users')
                        ->where('id = ?', $_POST['userId'])
                        ->execute()
                        ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                        ->fetch();
                
                $userImage = \dibi::select('*')
                        ->from('files')
                        ->where('model_id = ?', $_POST['userId'])
                        ->where('model_type = ?', MODEL_USER)
                        ->where('type = ?', 'profile')
                        ->execute()
                        ->setFormat(\dibi::FIELD_DATE, 'Y-m-d')
                        ->fetch();
                
                $user['base64image'] = $userImage;

                echo json_encode(['succes' => true, 'user' => $user], JSON_PRETTY_PRINT);
            } catch (Dibi\Exception $e) {
                echo json_encode(['update_user_error' => true], JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(['errors' => $error], JSON_PRETTY_PRINT);
        }
    }

}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
