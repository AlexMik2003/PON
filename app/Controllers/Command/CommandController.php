<?php

namespace App\Controllers\Command;

/**
 * Class CommandController - get information about device using SNMP
 *
 * @package App\Controllers\Command
 */
class CommandController
{
    /**
     * Config file
     *
     * @var string
     */
    protected $device_config;

    /**
     * CommandController constructor.
     */
    public function __construct()
    {
        $configFile = ROOT_PATH."/data/config.json";
        $configFile = file_get_contents($configFile);

        if (empty($configFile)) {
            throw new \BadMethodCallException('Configuration file is empty.');
        }

        $this->device_config = json_decode($configFile);
    }

    /**
     * Get array of device mibs
     *
     * @param string $model - device model
     *
     * @return array -  get device mibs
     */
    public function getDeviceMibs($model)
    {
        $mibs = [];
        foreach ($this->device_config as $key => $value)
        {
            if($model == $key)
            {
                $mibs = $value->mibs;
            }

        }

        return $mibs;
    }

    /**
     * Get all port indexes
     *
     * @param string $ip - device ip address
     *
     * @param string $community - snmp community
     *
     * @param string $mib - current mib
     *
     * @return array - ports indexes
     */
    public function getPorts($ip, $community, $mib)
    {
        $ports = snmp2_walk($ip,$community,$mib);
        $port_id = [];
        foreach ($ports as $value)
        {
            $value = explode(" ",$value);
            $port_id[] = $value[1];
        }

        return $port_id;
    }

    /**
     * Get all epons indexes
     *
     * @param string $ip - device ip address
     *
     * @param string $community - snmp community
     *
     * @param string $mib - current mib
     *
     * @return array - epons indexes
     */
    public function getEpons($ip, $community, $mib, $ports)
    {
        $epon = [];
        foreach ($ports as $value)
        {
            $pon = snmp2_walk($ip,$community,$mib.$value);
            $pon = explode(" ",$pon[0]);
            if($pon[1] == 1)
            {
                $epon[] = $value;
            }
        }

        unset($epon[count($epon)-1]);
        return $epon;

    }

    /**
     * Get epons aliases
     *
     * @param string $ip - device ip address
     *
     * @param string $community - snmp community
     *
     * @param string $mib - current mib
     *
     * @return array - epons aliases
     */
    public function getEponsAlias($ip, $community, $mib, $epons)
    {
        $alias = [];
        foreach ($epons as $value)
        {
            $pon = snmp2_walk($ip,$community,$mib.$value);
            $pon = explode(" ",$pon[0]);
            $pon = str_replace('"', "", $pon[1]);
            $alias[] = $pon;
        }
        return $alias;
    }

    /**
     * Get epons mac addresses
     *
     * @param string $ip - device ip address
     *
     * @param string $community - snmp community
     *
     * @param string $mib - current mib
     *
     * @return array - mac addresses
     */
    public function getBdcoEponMac($ip, $community, $mib, $ports)
    {
        $mac = [];
        foreach ($ports as $value)
        {
            $pon = snmp2_walk($ip,$community,$mib.$value);
            $pon = explode("Hex-STRING:",$pon[0]);
            $pon_mac = explode(" ",$pon[1]);
            $mac[] = $pon_mac[1].$pon_mac[2].".".$pon_mac[3].$pon_mac[4].".".$pon_mac[5].$pon_mac[6];

        }
       return $mac;
    }

    /**
     * Get onu status
     *
     * @param string $ip - device ip address
     *
     * @param string $community - snmp community
     *
     * @param string $mib - current mib
     *
     * @return array - onu status
     */
    public function getBdcoEponOperStatus($ip, $community, $mib, $ports)
    {
        $oper = [];
        foreach ($ports as $value)
        {
            $pon = snmp2_walk($ip,$community,$mib.$value);
            $pon = explode(" ",$pon[0]);
            $oper[] = $pon[1];

        }
        return $oper;
    }

}