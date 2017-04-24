<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User - users
 *
 * @package App\Models
 */
class Users extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "users";

    /**
     * Fillable table data
     *
     * @var array
     */
    protected $fillable = [
        'login',
        'password',
        'created',
        'updated',
    ];

    /**
     * Timestamp
     *
     * @var bool
     */
    public $timestamps = false;


}