<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ChoiceTranslation extends Authenticatable {

    use Notifiable;

    protected $table = 'files_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_id', 'lang', 'title', 'description', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function language() {
        return $this->belongsTo('App\Language');
    }

    public function file() {
        return $this->belogsTo('App\File');
    }

}
