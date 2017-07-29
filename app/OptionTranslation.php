<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cocur\Slugify\Slugify;
use DB;

class OptionTranslation extends Authenticatable {

    use Notifiable;

    protected $table = 'options_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'option_id', 'lang', 'value', 'slug', 'active'
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

    public function option() {
        return $this->belogsTo('App\Option');
    }

    public function getSlug() {
        $slugify = new Slugify();
        return $slugify->slugify($this->value);
    }

    public function setSlug() {
        $slugify = new Slugify();
        $slug = $slugify->slugify($this->value);
        if (strlen($slug) > 1) {
            DB::table('options_translations')
                    ->where('id', $this->id)
                    ->update(['slug' => $slug]);
        }
    }

}

