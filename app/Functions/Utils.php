<?php

namespace App\Functions;

use App\Select;
use App\Option;
use App\OptionTranslation;
use App\OptionPivot;
use App\File;
use App\FileTranslation;
use App\TermType;
use App\Term;
use App\TermTranslation;
use App\TermPivot;
use Cocur\Slugify\Slugify;

/** Vygenerovani pratelske URL adresy
 * http://programujte.com/clanek/2006092301-vytvarime-srozumitelne-url-adresy-z-nazvu-clanku/
 * @param string $title retezec, ze ktereho vygenerujeme url adresu
 * @return string $address vraceny retezec obsahujici friendly url
 */
class Utils {

    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }

    public static function insertOption($title, $type, $lang, $value, $model_id, $mode_type) {
        $value = trim($value);
        if (!empty($value)) {
            $selectId = Select::firstOrCreate(['title' => $title, 'type' => $type])->id;
            $optionId = Option::firstOrCreate(['select_id' => $selectId, 'slug' => Utils::slug($value)])->id;
            $optionTranslationId = OptionTranslation::firstOrCreate(['option_id' => $optionId, 'lang' => $lang, 'value' => ucfirst($value), 'slug' => Utils::slug($value)])->id;
            $pivot = OptionPivot::firstOrCreate(['option_id' => $optionId, 'model_id' => $model_id, 'model_type' => $mode_type]);
        }
    }

    public static function insertTerm($termType, $title, $lang, $model_id, $mode_type) {
        $title = trim($title);
        if (!empty($title)) {
            $termTypeId = TermType::firstOrCreate(['name' => $termType])->id;
            $termId = Term::firstOrCreate(['term_type_id' => $termTypeId, 'slug' => Utils::slug($title)])->id;
            $termTranslationId = TermTranslation::firstOrCreate(['term_id' => $termId, 'lang' => $lang, 'title' => ucfirst($title), 'slug' => Utils::slug($title)])->id;
            $pivot = TermPivot::firstOrCreate(['term_id' => $termId, 'model_id' => $model_id, 'model_type' => $mode_type]);
        }
    }

    public static function insertActor($actor, $model_id, $mode_type) {

        $actorId = \App\Actor::updateOrCreate(
                        ['thetvdb_id' => $actor['thetvdb_id']], [
                            'thetvdb_id' => $actor['thetvdb_id'],
                            'name' => $actor['name'],
                            'slug' => $actor['slug'],
                            'role' => $actor['role'],
                            'sort' => $actor['sort']
                        ]
                )->id;


        if ($actor['image']) {
            $fileArr = [
                'type' => 'thumb',
                'extension' => 'jpg',
                'external_patch' => 'http://thetvdb.com/banners/' . $actor['image'],
                'model_id' => $actorId,
                'model_type' => 'App\Actor',
                'base64' => ''];

            $file = File::updateOrCreate(['external_patch' => 'http://thetvdb.com/banners/' . $actor['image']], $fileArr);
        }

        $pivot = \App\ActorPivot::updateOrCreate(['actor_id' => $actorId, 'model_id' => $model_id, 'model_type' => $mode_type], ['actor_id' => $actorId, 'model_id' => $model_id, 'model_type' => $mode_type]);
    }

    //Q = primereny pocet hodnoceni..
    //https://math.stackexchange.com/questions/942738/algorithm-to-calculate-rating-based-on-multiple-reviews-using-both-review-score.
    public static function score($rating, $ratingCount, $Q = 144, $P = 0.5) {
        return ($P * $rating + 10 * ( 1 - $P) * (1 - pow(M_E, -$ratingCount / $Q)));
    }

    public static function url_get_contents($Url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'method' => "GET",
            'header' => "Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n" . // check function.stream-context-create on php.net
            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
        ));
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        // Check HTTP status code
        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    break;
                default:
                    echo 'Unexpected HTTP code: ' . $http_code . " at" . $Url . "<br/>\n";
                    sleep(3);
                    curl_close($ch);
                    return;
            }
        }

        curl_close($ch);
        return $output;
    }

    public static function slug($title) {
        $slugify = new Slugify();
        $slug = $slugify->slugify($title);
        return $slug;
    }

    public static function get_numerics($str) {
        preg_match_all('/\d+/', $str, $matches);
        return $matches[0];
    }

    public static function age_start($age) {
        return date('Y-m-d', strToTime("now - $age years"));
    }

    public static function validDate($date) {
        return (bool) strtotime($date);
    }

    public static function remote_file_exists($patch) {
        $ch = curl_init($patch);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //$retcode >= 400 -> not found, $retcode = 200, found.
        curl_close($ch);
        if ($retcode == 200) {
            return true;
        } else {
            return false;
        }
    }

    function compareFiles($file_a, $file_b) {
        if (filesize($file_a) == filesize($file_b)) {
            
            $fp_a = fopen($file_a, 'rb');
            $fp_b = fopen($file_b, 'rb');

            while (($b = fread($fp_a, 4096)) !== false) {
                $b_b = fread($fp_b, 4096);
                if ($b !== $b_b) {
                    fclose($fp_a);
                    fclose($fp_b);
                    return false;
                }
            }

            fclose($fp_a);
            fclose($fp_b);

            return true;
        }

        return false;
    }

}
