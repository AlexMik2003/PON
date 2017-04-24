<?php

namespace App\Helpers;

use App\Models\Network;


/**
 * Class IP
 *
 * @package App\Helpers
 */
class IP
{
    /**
     * IP mask
     */
    const NETMASK = '255.255.255.255';

    /**
     * @var array of private ip
     */
    public $privateRange = [
        "A" => [
            "start" => "10.0.0.0",
            "end" => "10.255.255.255",
        ],
        "B" => [
            "start" => "172.16.0.0",
            "end" => "172.31.255.255",
        ],
        "C" => [
            "start" => "192.168.0.0",
            "end" => "192.168.255.255",
        ],
    ];

    /**
     * Convert ip address to long integer
     *
     * @param string $ip - ip address in string format with dot
     *
     * @return integer
     */
    public function convertIp2Long($ip)
    {
        return ip2long($ip);
    }

    /**
     * Convert long integer to ip address
     *
     * @param string $long - ip address in long integer format
     *
     * @return integer
     */
    public function convertLong2IP($long)
    {
        return long2ip($long);
    }

    /**
     * Convert netmask to a cidr mask
     *
     * @param integer $mask - ip mask in long integer
     *
     * @return integer
     */
    public function mask2Cidr($mask)
    {
        $base_mask = $this->convertIp2Long(self::NETMASK);
        return 32-log(($mask ^ $base_mask)+1,2);
    }

    /**
     * Get ip network class
     *
     * @param integer $mask
     *
     * @return string
     */
    public function getIPClass($net)
    {
        $class = '';
        $net = decbin($net);
        if(substr($net,0,3)=='110')
        {
            $class = "C";
        }
        elseif(substr($net,0,3)==='100')
        {
            $class = "B";
        }
        else{
            $class = "A";
        }

        return $class;

    }

    /**
     * Get ip network type
     *
     * @param integer $net - ip network
     *
     * @return string
     */
    public function getIPType($net)
    {
        $class = "Public";
        $query= Network::
        whereBetween("network",[$this->convertIp2Long($this->privateRange["A"]["start"]),$this->convertIp2Long($this->privateRange["A"]["end"])])->
        orWhereBetween("network",[$this->convertIp2Long($this->privateRange["B"]["start"]),$this->convertIp2Long($this->privateRange["B"]["end"])])->
        orWhereBetween("network",[$this->convertIp2Long($this->privateRange["C"]["start"]),$this->convertIp2Long($this->privateRange["C"]["end"])])->
        get();

        foreach ($query as $item)
        {
            if($item->network==$net)
            {
                $class = "Private";
            }
        }

        return $class;
    }

    /**
     * Get ip hosts count
     *
     * @param integer $mask - ip mask
     *
     * @return number
     */
    public function networkHosts($mask)
    {
        $mask = decbin($mask);
        $count = 0;
        foreach (count_chars($mask,1) as $key => $value)
        {
            if(chr($key)=='0')
            {
                $count = $value;
            }
        }

        return pow(2,$count)-2;
    }

    /**
     * Check is ip in network
     *
     * @param string $ip
     *
     * @param string $net
     *
     * @param string $mask
     *
     * @return bool
     */
    public function checkIP($ip,$net,$mask)
    {
        $ip = $this->convertIp2Long($ip);
        $net = $this->convertIp2Long($net);
        $mask=pow(2,32-$mask)-1;

        $net = $net&~$mask;
        if (!(($ip^$net)&~$mask))
        {
            return true;
        }
        return false;
    }

    /**
     * Get used ip in network
     *
     * @param int $count
     *
     * @return float|int
     */
    public function percentUseIP($count)
    {
        return 255/100*$count;
    }

}