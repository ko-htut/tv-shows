<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Intervention\Image;
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
            return $this->patch;
        }

        if (isset($this->external_patch) && !empty($this->external_patch)) {
            return $this->external_patch;
        }

        return null;
    }

    public function getSrc($width, $height = null) {
        
    }

    public function resize($width, $height = null, $quality = 90, $type = 'normal') {
        
        $img = Image::make($this->patch);
        //$img->resize($width);
        //$img->save('public/bar.jpg');
    }

}
