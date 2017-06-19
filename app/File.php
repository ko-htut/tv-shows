<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

}
