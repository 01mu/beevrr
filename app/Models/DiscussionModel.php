<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionModel extends Model
{
    protected $table = 'discussions';
    public $timestamps = false;
    protected $dateFormat = 'U';
}
