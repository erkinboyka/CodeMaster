<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapList extends Model
{
    protected $table = 'roadmap_list';

    protected $fillable = [
        'title',
        'description',
    ];
}
