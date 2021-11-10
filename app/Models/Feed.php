<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    protected $table = 'feeds';

    protected $fillable = [
        'title',
        'link',
        'source',
        'source_url',
        'publish_date',
        'description'
    ];
}
