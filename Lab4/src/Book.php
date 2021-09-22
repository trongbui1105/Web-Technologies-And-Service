<?php

namespace CT275\Lab4;

use \Illuminate\Database\Eloquent\Model;


class Book extends Model
{
    protected $fillable = ['title', 'price', 'pages_count', 'description'];
    // protected $table = 'books';
    public $timestamps = false;
    public function author()
    {
        return $this->belongsTo('\CT275\Lab4\Author', 'author_id');
    }


}
