<?php

namespace App\Http\Controllers;

use DB;
use App\Episode;

define('SITE_URL', 'http://www.serialovna.cz');

class SitemapController extends Controller {

    public function pages() {
        $sitemap = \App::make("sitemap");
        $langs = [
            'cs', 'en', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'pt', 'sl', 'sv', 'no', 'hr', 'he', 'ja', 'ko', 'zh'
        ];

        $xmlName = 'sitemaps/pages-all';

        foreach ($langs as $lang) {
            $prefix = ($lang == DEF_LANG) ? '/' : '/' . $lang;

            $sitemap->add(SITE_URL . $prefix, null, 0.9, 'daily'); //Homepages

            if (DEF_LANG == $lang) {
                $sitemap->add(SITE_URL . '/actors', null, 0.5, 'monthly'); //Actors
            }
        }

        $sitemap->store('xml', $xmlName);
        $sitemap->model->resetItems();
    }

    public function shows() {
        $sitemap = \App::make("sitemap");
        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'pt', 'sl', 'sv', 'no', 'hr', 'he', 'ja', 'ko', 'zh'
        ];
        $limit = 25000;
        foreach ($langs as $lang) {
            $prefix = ($lang == DEF_LANG) ? '' : $lang . '/';
            //-----------------------------START SHOWS-----------------------------
            $count = \App\Show::count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/shows-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $shows = \App\Show::offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($shows as $s) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->model->resetItems();
                    }
                    $sitemap->add(SITE_URL . $s->url($lang), $s->updated_at, 0.6, 'weekly');
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->model->resetItems();
            }

            //-----------------------------END SHOWS--------------------------------
        }//end langs
        //$sitemap->store('sitemapindex', 'sitemap');
    }

    public function episodes() {

        
        $sitemap = \App::make("sitemap");

        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'pt', 'sl', 'sv', 'no', 'hr', 'he', 'ja', 'ko', 'zh'
        ];
       
        $limit = 25000;

        foreach ($langs as $lang) {
            $prefix = ($lang == DEF_LANG) ? '' : $lang . '/';

            //-----------------------------START EPISODES---------------------------

            $count = DB::table('episodes_translations')
                    ->select(['episodes.id'])
                    ->join('episodes', 'episodes.id', '=', 'episode_id')
                    ->join('shows_translations', 'shows_translations.show_id', '=', 'episodes.show_id')
                    ->whereRaw('shows_translations.lang = "' . $lang . '" AND season_number > 0 AND episode_number > 0 AND episodes_translations.lang = "' . $lang . '" AND (episodes_translations.title IS NOT NULL OR episodes_translations.content IS NOT NULL)')
                    ->count();

            $pages = intval(ceil($count / $limit));


            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/episodes-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;

                $episodes = DB::table('episodes_translations')
                        ->select(['episodes.id as id', 'shows_translations.slug as show_slug', 'episode_number', 'season_number', 'episodes_translations.updated_at as updated_at', 'shows_translations.show_id as show_id'])
                        ->join('episodes', 'episodes.id', '=', 'episode_id')
                        ->join('shows_translations', 'shows_translations.show_id', '=', 'episodes.show_id')
                        ->whereRaw('shows_translations.lang = "' . $lang . '" AND season_number > 0 AND episode_number > 0 AND episodes_translations.lang = "' . $lang . '" AND (episodes_translations.title IS NOT NULL OR episodes_translations.content IS NOT NULL)')
                        ->offset($offset)
                        ->limit($limit)
                        ->get();

                $counter = 0;

                foreach ($episodes as $e) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->model->resetItems();
                    }
                    //$url = Episode::find($e->id)->url($lang);
                    $showSlug = isset($e->show_slug) ? $e->show_slug : $e->show_id;
                    $url = '/'. $prefix . 'shows/' .  $showSlug . '/s' . str_pad($e->season_number, 2, '0', STR_PAD_LEFT) . 'e' . str_pad($e->episode_number, 2, '0', STR_PAD_LEFT);
        
                    $sitemap->add(SITE_URL . $url, $e->updated_at, 0.3, 'monthly');
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->model->resetItems();
            }

            //-----------------------------END EPISODES-----------------------------
        }//end langs
    }

    public function actors() {
        $sitemap = \App::make("sitemap");
        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'pt', 'sl', 'sv', 'no', 'hr', 'he', 'ja', 'ko', 'zh'
        ];

        $limit = 25000;

        foreach ($langs as $lang) {
            $prefix = ($lang == DEF_LANG) ? '' : $lang . '/';

            //-----------------------------START ACTORS-----------------------------
            $count = \App\Actor::count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/actors-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $actors = \App\Actor::offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($actors as $a) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->model->resetItems();
                    }

                    $pref = '';
                    if ($lang == 'cs') {
                        $pref = $prefix;
                    } else {
                        $pref = '/' . $lang;
                    }
                    $sitemap->add(SITE_URL . $pref . $a->url(), $a->updated_at, 0.3, 'monthly');
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName . $page);
                $sitemap->model->resetItems();
            }

            //-----------------------------END ACTORS--------------------------------
        }//end langs
    }

    public function genres() {

        $sitemap = \App::make("sitemap");
        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'pt', 'sl', 'sv', 'no', 'hr', 'he', 'ja', 'ko', 'zh'
        ];

        $limit = 25000;

        foreach ($langs as $lang) {
            $prefix = ($lang == DEF_LANG) ? '' : $lang . '/';

            //-----------------------------START GENRES-----------------------------
            $count = \App\Term::count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/terms-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $terms = \App\Term::offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($terms as $t) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->model->resetItems();
                    }
                    $sitemap->add(SITE_URL . '/' . $prefix . 'genres/' . $t->slug, $t->updated_at, 0.6, 'weekly'); //to do translations nextime
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                $sitemap->model->resetItems();
            }

            //-----------------------------END GENRES-------------------------------
        }//end langs
    }

    public function networks() {
        $sitemap = \App::make("sitemap");
        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'pt', 'sl', 'sv', 'no', 'hr', 'he', 'ja', 'ko', 'zh'
        ];

        $limit = 25000;

        foreach ($langs as $lang) {
            $prefix = ($lang == DEF_LANG) ? '' : $lang . '/';

            //-----------------------------START NETWORKS-----------------------------
            $selectId = \App\Select::where('title', '=', 'network')->first()->id;
            $count = \App\Option::where('select_id', '=', $selectId)->count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/networks-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $networks = \App\Option::where('select_id', '=', $selectId)->offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($networks as $n) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->model->resetItems();
                    }
                    $sitemap->add(SITE_URL . '/' . $prefix . 'networks/' . $n->slug, $n->updated_at, 0.6, 'weekly'); //to do translations nextime
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->model->resetItems();
            }

            //-----------------------------END NETWORKS-------------------------------
        }//end langs
    }

    /* !Execution time! */

    public function full() {
        $sitemap = \App::make("sitemap");
        $langs = [
            'en', 'cs', 'de', 'es', 'fr', 'pl', 'ru', 'da', 'fi', 'nl', 'it', 'hu', 'el', 'tr', 'pt', 'sl', 'sv', 'no', 'hr', 'he', 'ja', 'ko', 'zh'
        ];

        $limit = 25000;

        foreach ($langs as $lang) {
            $prefix = ($lang == DEF_LANG) ? '' : $lang . '/';

            //-----------------------------START SHOWS-----------------------------
            $count = \App\Show::count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/shows-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $shows = \App\Show::offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($shows as $s) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                        $sitemap->model->resetItems();
                    }
                    $sitemap->add(SITE_URL . $s->url($lang), $s->updated_at, 0.6, 'weekly');
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                $sitemap->model->resetItems();
            }

            //-----------------------------END SHOWS--------------------------------
            //-----------------------------START EPISODES---------------------------
            $count = DB::table('episodes_translations')
                    ->select(['shows_translations.slug as show_slug', 'episode_number', 'season_number', 'episodes_translations.updated_at'])
                    ->join('episodes', 'episodes.id', '=', 'episode_id')
                    ->join('shows_translations', 'shows_translations.show_id', '=', 'episodes.show_id')
                    ->whereRaw('shows_translations.lang = "' . $lang . '" AND season_number > 0 AND episode_number > 0 AND episodes_translations.lang = "' . $lang . '" AND (episodes_translations.title IS NOT NULL OR episodes_translations.content IS NOT NULL)')
                    ->count();

            $pages = intval(ceil($count / $limit));


            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/episodes-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;

                $episodes = DB::table('episodes_translations')
                        ->select(['shows_translations.slug as show_slug', 'episode_number', 'season_number', 'episodes_translations.updated_at'])
                        ->join('episodes', 'episodes.id', '=', 'episode_id')
                        ->join('shows_translations', 'shows_translations.show_id', '=', 'episodes.show_id')
                        ->whereRaw('shows_translations.lang = "' . $lang . '" AND season_number > 0 AND episode_number > 0 AND episodes_translations.lang = "' . $lang . '" AND (episodes_translations.title IS NOT NULL OR episodes_translations.content IS NOT NULL)')
                        ->offset($offset)
                        ->limit($limit)
                        ->get();

                $counter = 0;

                foreach ($episodes as $e) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                        $sitemap->model->resetItems();
                    }

                    $sitemap->add(SITE_URL . '/' . $prefix . 'shows/' . $e->show_slug . '/s' . str_pad($e->season_number, 2, '0', STR_PAD_LEFT) . 'e' . str_pad($e->episode_number, 2, '0', STR_PAD_LEFT), $e->updated_at, 0.3, 'monthly');
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                $sitemap->model->resetItems();
            }

            //-----------------------------END EPISODES-----------------------------
            //-----------------------------START ACTORS-----------------------------
            $count = \App\Actor::count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/actors-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $actors = \App\Actor::offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($actors as $a) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                        $sitemap->model->resetItems();
                    }

                    $pref = '';
                    if ($lang == 'cs') {
                        $pref = $prefix;
                    } else {
                        $pref = '/' . $lang;
                    }
                    $sitemap->add(SITE_URL . $pref . $a->url(), $a->updated_at, 0.3, 'monthly');
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName . $page);
                $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                $sitemap->model->resetItems();
            }

            //-----------------------------END ACTORS--------------------------------
            //-----------------------------START GENRES-----------------------------
            $count = \App\Term::count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/terms-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $terms = \App\Term::offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($terms as $t) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                        $sitemap->model->resetItems();
                    }
                    $sitemap->add(SITE_URL . '/' . $prefix . 'genres/' . $t->slug, $t->updated_at, 0.6, 'weekly'); //to do translations nextime
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                $sitemap->model->resetItems();
            }

            //-----------------------------END GENRES-------------------------------
            //-----------------------------START NETWORKS-----------------------------
            $selectId = \App\Select::where('title', '=', 'network')->first()->id;
            $count = \App\Option::where('select_id', '=', $selectId)->count();
            $pages = intval(ceil($count / $limit));

            for ($page = 1; $page <= $pages; $page++) {
                $xmlName = 'sitemaps/networks-' . $lang . '-' . $page;
                $offset = $page * $limit - $limit;
                $networks = \App\Option::where('select_id', '=', $selectId)->offset($offset)->limit($limit)->get();
                $counter = 0;
                foreach ($networks as $n) {
                    $counter++;
                    if ($counter == $limit) {
                        $sitemap->store('xml', $xmlName);
                        $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                        $sitemap->model->resetItems();
                    }
                    $sitemap->add(SITE_URL . '/' . $prefix . 'networks/' . $n->slug, $n->updated_at, 0.6, 'weekly'); //to do translations nextime
                }
            }

            if (!empty($sitemap->model->getItems())) {
                $sitemap->store('xml', $xmlName);
                $sitemap->addSitemap(secure_url($xmlName . '.xml'));
                $sitemap->model->resetItems();
            }


            //-----------------------------END NETWORKS-------------------------------
        }//end langs
        $sitemap->store('sitemapindex', 'sitemap');
    }

    public function sitemapindex() {
        // create sitemap index
        $sitemap = \App::make("sitemap");
        $files = \File::allFiles(public_path() . '/sitemaps');
        foreach ($files as $file) {
            if ($file->getExtension() == "xml") {
                $sitemap->addSitemap(SITE_URL . '/public/sitemaps/' . $file->getFilename(), null);
            }
        }
        // create file sitemap.xml in your public folder (format, filename)
        $sitemap->store('sitemapindex', 'sitemap');
    }

    public function generate() {

        $rand = rand(0, 2);

        if ($rand == 0) {
            $this->pages();
            $this->networks();
            $this->genres();
            $this->actors();
        }

        if ($rand == 1) {
            $this->shows();
        }

        if ($rand == 2) {
            $this->episodes();
        }

        $this->sitemapindex();
    }

}
