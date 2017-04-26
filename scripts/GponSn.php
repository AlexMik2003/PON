<?php

require_once __DIR__."/../bootstrap/app.php";

use App\Models\Device;
use \App\Models\GponSn;
use App\Helpers\IP;
use  \App\Controllers\Command\CommandController;

$device_config = new CommandController();
$ip = new IP();

$container["db"]->table("gpon_sn")->truncate();

$device = Device::where("type","=","raisecom")->get();
foreach ($device as $item)
{

    $mibs = $device_config->getDeviceMibs("raisecom");
    $gpons = $device_config->getPorts($ip->convertLong2IP($item->ip),$container["community"],$mibs->gpons);
    $alias = $device_config->getGponsAlias($ip->convertLong2IP($item->ip),$container["community"],$mibs->alias, $gpons);
    $sn = $device_config->getRaisecomSn($ip->convertLong2IP($item->ip),$container["community"],$mibs->gpon_sn, $gpons);
    $oper = $device_config->getOperStatus($ip->convertLong2IP($item->ip),$container["community"],$mibs->oper_status, $gpons);

    for($i=0;$i<count($gpons);$i++)
    {
        GponSn::create([
            "device_id" => $item->id,
            "gpon" => strtoupper($alias[$i]),
            "sn" => $sn[$i],
            "oper_status" => $oper[$i],
        ]);
    }
}