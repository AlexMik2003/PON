<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Device - device
 *
 * @package App\Models
 */
class Device extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "device";

    /**
     * Fillable table data
     *
     * @var array
     */
    protected $fillable = [
        'ip',
        'name',
        'type',
        'latency',
    ];

    /**
     * Timestamp
     *
     * @var bool
     */
    public $timestamps = false;

    public function eponmac()
    {
        return $this->belongsTo(EponMac::class);
    }
}