<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Image;
use App\Functions\Utils;

//use Intervention\Image\Facades\Image as Image;

class File extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'patch', 'external_patch', 'type', 'extension', 'file_size', 'active', 'model_type', 'model_id', 'sort'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Get the object record associated with the object. - Defining The Inverse Of The Relationship
     */
    public function translations() {
        return $this->hasMany('App\FileTranslation');
    }

    public function translation($lang = null) {
        if ($lang == null) {
            $lang = App::getLocale();
        }
        return $this->hasMany('App\FileTranslation', 'file_id', 'id')->where('lang', '=', $lang)->first();
    }

    public function src() {
        if (isset($this->patch) && !empty($this->patch)) {
            return 'http://' . $_SERVER['SERVER_NAME'] . $this->patch;
        }

        if (isset($this->external_patch) && !empty($this->external_patch)) {
            return $this->external_patch;
        }

        return null;
    }

    public function getSrc($width, $type = 'normal') {
        
        
        //return $this->src();//Temp. Fix 
        
        if ($type == 'normal') {
            $src = $this->resizeTo($width);
        } else if ($type == 'thumb') {
            $src = $this->resizeToThumb($width);
        }

        return $src;
    }

    public function resizeTo($width, $height = null, $quality = 90) {

        $ext = $this->ext();
        $patch = 'public/img/' . strtolower(str_replace("\\", '', str_replace('App', '', $this->model_type))) . 's/';
        $filename = $this->type . '-' . $this->model_id . '-' . $this->id . '-w' . $width . 'px' . '.' . $ext;
        $full_patch = $patch . $filename;
        $remote_full_patch = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $full_patch;

        if (Utils::remote_file_exists($remote_full_patch)) {
            return $remote_full_patch;
        }

        $img = Image::make(Utils::url_get_contents($this->src()));
        //$img->resize($width);
        $img->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($full_patch, $quality);

        return $remote_full_patch;
    }

    public function resizeToThumb($width, $height = null, $quality = 90) {

        $ext = $this->ext();
        $patch = 'public/img/' . strtolower(str_replace("\\", '', str_replace('App', '', $this->model_type))) . 's/';
        $filename = $this->type . '-' . $this->model_id . '-' . $this->id . '-w' . $width . 'px' . '-thumb.' . $ext;
        $full_patch = $patch . $filename;
        $remote_full_patch = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $full_patch;

        if (Utils::remote_file_exists($remote_full_patch)) {
            //return $remote_full_patch;
        }

        $img = Image::make(Utils::url_get_contents($this->src()));
        //$img->resize($width);
        $img->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->fit($width, $width, null, 'top')->save($full_patch, $quality);

        return $remote_full_patch;
    }

    /* File extension */

    public function ext() {
        $ext = pathinfo($this->src(), PATHINFO_EXTENSION);
        if (!isset($ext) or empty($ext)) {
            $headers = get_headers($this->src(), true);
            if (isset($headers['Content-Type'])) {
                $pieces = explode('/', $headers['Content-Type']);
                $ext = strtok(end($pieces), '+');
            } else {
                $ext = 'jpg';
            }
        }
        return strtok($ext, '?');
    }

}
