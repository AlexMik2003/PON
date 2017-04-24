<?php

require_once __DIR__."/../bootstrap/app.php";

use App\Models\Device;
use App\Helpers\IP;
use \JJG\Ping;

$ip = new IP();
$ttl = 64;
$timeout = 5;

$device = Device::get();

foreach ($device as $item)
{
    $ping = new Ping($ip->convertLong2IP($item->ip),$ttl,$timeout);
    $latency = $ping->ping();
    $status = $latency ? 1 : 0;
    Device::where("id","=",$item->id)->update([
       "latency" => $status,
    ]);
}
