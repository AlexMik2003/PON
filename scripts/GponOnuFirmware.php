<?php

require_once __DIR__."/../bootstrap/app.php";

use \App\Models\GponOnu;
use App\Helpers\IP;
use  \App\Controllers\Command\CommandController;
use phpseclib\Net\SSH2;

$device_config = new CommandController();
$ip = new IP();
$query = GponOnu::get();

foreach ($query as $onu)
{
    $gpon = explode(" ",$onu->gpon);

    $ssh = new SSH2($ip);
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
    $ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu {$gpon[2]}\n");
    echo $ssh->read();
    $ssh->write("commit system2 gpon-onu {$gpon[2]}\n");
    echo $ssh->read();
    $ssh->write("reboot gpon-onu  {$gpon[2]}\n");
    echo $ssh->read();
    $ssh->write("wr startup-config\n");
    echo $ssh->read();
    $ssh->write("exit\n");
    echo $ssh->read();
    $ssh->write("exit\n");
}
