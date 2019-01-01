<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

class VoteModel extends Model
{
    protected $table = 'votes';
    public $timestamps = false;
    protected $dateFormat = 'U';
}
