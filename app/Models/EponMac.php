<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EponMac extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "epon_mac";

    /**
     * Fillable table data
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'epon',
        'mac',
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