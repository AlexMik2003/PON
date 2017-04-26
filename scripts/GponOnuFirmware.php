<?php

require_once __DIR__."/../bootstrap/app.php";

use \App\Models\GponOnu;
use \App\Models\GponSn;
use App\Models\Device;
use App\Helpers\IP;
use  \App\Controllers\Command\CommandController;
use phpseclib\Net\SSH2;

$device_config = new CommandController();
$ip = new IP();
$query = GponOnu::get();

foreach ($query as $onu)
{
    $raise = GponSn::where("sn","=",$onu->sn)->first();
    $gpon = explode(" ",$raise->gpon);
    $device = Device::where("id","=",$raise->device_id)->first();

    $ssh = new SSH2($ip->convertLong2IP($device->ip));
    if (!$ssh->login("kuzmich", 'Fibra.Net')) {
        exit('Login Failed');
    }
    $ssh->write("kuzmich\n");
    echo $ssh->read();
    $ssh->write("Fibra.Net\n");
    echo $ssh->read();
    $ssh->write("enable\n");
    echo $ssh->read();
    $ssh->write("dfvgbh99\n");
    echo $ssh->read();
    $ssh->write("download system1 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu {$gpon[2]}\n");
    $ssh->setTimeout(300);
    echo $ssh->read();
    $ssh->setTimeout(1);
    $ssh->write("commit system2 gpon-onu {$gpon[2]}\n");
    echo $ssh->read();
    $ssh->write("reboot gpon-onu  {$gpon[2]}\n");
    echo $ssh->read();
    $ssh->write("yes\n");
    echo $ssh->read();
    $ssh->write("wr startup-config\n");
    echo $ssh->read();
    $ssh->write("exit\n");
    echo $ssh->read();
    $ssh->write("exit\n");

}
