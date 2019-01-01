<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseModel extends Model
{
    protected $table = 'responses';
    public $timestamps = false;
    protected $dateFormat = 'U';
}
