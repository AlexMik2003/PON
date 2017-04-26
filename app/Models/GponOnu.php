<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GponOnu extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "gpon_onu";

    /**
     * Fillable table data
     *
     * @var array
     */
    protected $fillable = [
        'sn',
    ];

    /**
     * Timestamp
     *
     * @var bool
     */
    public $timestamps = false;

    public function device()
    {
        return $this->hasOne(Device::class);
    }
}