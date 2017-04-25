<?php

require_once __DIR__."/../bootstrap/app.php";

use phpseclib\Net\SSH2;


$ssh = new SSH2($ip);
if (!$ssh->login("kuzmich", 'Fibra.Net')) {
    exit('Login Failed');
}
$ssh->write("avm\n");
echo $ssh->read();
$ssh->write("Z@dnitsa\n");
echo $ssh->read();
$ssh->write("enable\n");
echo $ssh->read();
$ssh->write("dfvgbh99\n");
echo $ssh->read();
$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/1/1-32\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/1/1-32\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/1/1-32\n");
echo $ssh->read();
$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/1/33-64\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/1/33-64\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/1/33-64\n");
echo $ssh->read();

$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/2/1-32\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/2/1-32\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/2/1-32\n");
echo $ssh->read();
$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/2/33-64\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/2/33-64\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/2/33-64\n");
echo $ssh->read();

$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/3/1-32\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/3/1-32\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/3/1-32\n");
echo $ssh->read();
$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/3/33-64\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/3/33-64\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/3/33-64\n");
echo $ssh->read();

$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/4/1-32\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/4/1-32\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/4/1-32\n");
echo $ssh->read();
$ssh->write("download system2 tftp 185.102.184.19 ISCOMHT803G-1GE_T_GJ01_SYSTEM_3.0.8_20161229 gpon-onu 1/4/33-64\n");
echo $ssh->read();
$ssh->write("yes\n");
echo $ssh->read();
$ssh->write("commit system2 gpon-onu 1/4/33-64\n");
echo $ssh->read();
$ssh->write("reboot gpon-onu  1/4/33-64\n");
echo $ssh->read();

$ssh->write("wr startup-config\n");
echo $ssh->read();
$ssh->write("exit\n");
echo $ssh->read();
$ssh->write("exit\n");