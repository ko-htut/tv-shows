<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cocur\Slugify\Slugify;

class ShowTranslation extends Authenticatable {

    use Notifiable;

    protected $table = 'shows_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_id', 'lang', 'title', 'slug', 'content', 'meta_title', 'meta_description', 'active',
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

    public function show() {
        return $this->belogsTo('App\Show');
    }

    public function getSlug() {
        $slugify = new Slugify();
        return $slugify->slugify($this->title);
    }

    public function setSlug() {
        $slugify = new Slugify();
        $slug = $slugify->slugify($this->title);
        if (strlen($slug) > 3) {
            DB::table('shows_translation')
                    ->where('id', $this->id)
                    ->update(['slug' => $slug]);
        }
    }

    public function getMetaDescriptionAttribute($value) {
        return !$value ? mb_substr($this->content, 0, 160) : $value;
    }
    
    public function getMetaTitleAttribute($value) {
        return !$value ? mb_substr($this->title, 0, 60) : $value;
    }

}
