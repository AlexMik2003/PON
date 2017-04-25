<?php

require_once __DIR__."/../bootstrap/app.php";

use App\Models\Device;
use App\Helpers\IP;
use  \App\Models\EponMac;
use \App\Models\GponSn;
use  \App\Controllers\Command\CommandController;



$ip = new IP();
$device_config = new CommandController();
$device = Device::get();
$container["db"]->table("epon_mac")->truncate();
$container["db"]->table("gpon_sn")->truncate();

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
            $mac = $device_config->getBdcomEponMac($ip->convertLong2IP($item->ip),$container["community"],$mibs->epon_mac, $epon_id);
            $oper = $device_config->getOperStatus($ip->convertLong2IP($item->ip),$container["community"],$mibs->oper_status, $epon_id);

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
}