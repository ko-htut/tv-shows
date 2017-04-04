<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TermTranslation extends Authenticatable {

    use Notifiable;

    protected $table = 'terms_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'term_id', 'lang', 'title', 'slug', 'description', 'meta_title', 'meta_description', 'active'
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

    public function term() {
        return $this->belogsTo('App\Term');
    }

}
