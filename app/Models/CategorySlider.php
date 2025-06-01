<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategorySlider extends Model
{
    use SoftDeletes;


    protected $fillable = [
        "slider_image_url",
        "slider_status"
    ];


    protected $casts = [
        "slider_status" => "boolean",
    ];
}
