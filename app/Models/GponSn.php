<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GponSn extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "gpon_sn";

    /**
     * Fillable table data
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'gpon',
        'sn',
        'oper_status',
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