<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class FooterSetting extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'footer_settings';
    protected $fillable = ['id' , 'menu', 'slug' ,'parent_id', 'link'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
        ->generateSlugsFrom('menu')
        ->saveSlugsTo('slug');
    }
}
