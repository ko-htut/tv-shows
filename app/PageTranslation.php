<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PageTranslation extends Authenticatable {

    use Notifiable;

    protected $table = 'pages_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_id', 'lang', 'title', 'slug', 'content', 'meta_title', 'meta_description', 'active',
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

    public function page() {
        return $this->belogsTo('App\Page');
    }

}
