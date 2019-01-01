<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityModel extends Model
{
    protected $table = 'activities';
    public $timestamps = false;
    protected $dateFormat = 'U';
}
