<?php

require_once __DIR__."/../bootstrap/app.php";

use App\Models\Device;
use App\Helpers\IP;
use  \App\Models\EponMac;
use  \App\Controllers\Command\CommandController;



$ip = new IP();
$device_config = new CommandController();

$device = Device::get();
$container["db"]->table("epon_mac")->truncate();

foreach ($device as $item)
{
      if($item->type == 'bdcom')
        {
            $mibs = $device_config->getDeviceMibs("bdcom");
            $ports = $device_config->getPorts($ip->convertLong2IP($item->ip),$container["community"],$mibs->ports);
            $epons = $device_config->getEpons($ip->convertLong2IP($item->ip),$container["community"],$mibs->epon, $ports);
            $epon_id = [];
            for($i=8;$i<count($epons);$i++)
            {
               $epon_id[] = $epons[$i];
            }
            $alias = $device_config->getEponsAlias($ip->convertLong2IP($item->ip),$container["community"],$mibs->alias, $epon_id);
            $mac = $device_config->getBdcoEponMac($ip->convertLong2IP($item->ip),$container["community"],$mibs->epon_mac, $epon_id);
            $oper = $device_config->getBdcoEponOperStatus($ip->convertLong2IP($item->ip),$container["community"],$mibs->oper_status, $epon_id);

            for($i=0;$i<count($epon_id);$i++)
            {
                EponMac::create([
                    "device_id" => $item->id,
                    "epon" => $alias[$i],
                    "mac" => $mac[$i],
                    "oper_status" => $oper[$i],
                ]);
            }

        }
        if($item->type == 'raisecom')
        {
            //$mibs = $this->device_config->getDeviceMibs("bdcom");
        }
}