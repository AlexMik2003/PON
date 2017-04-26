<?php

namespace App\Controllers\BDCOM;


use App\Controllers\BaseController;
use App\Models\Device;
use App\Models\EponMac;
use \Respect\Validation\Validator as valid;
use phpseclib\Net\SSH2;

/**
 * Class BdcomController - class for manage BDCOM
 *
 * @package App\Controllers\BDCOM
 */
class BdcomController extends BaseController
{
    /**
     * Show summary information about EPON interface
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and epon interface id
     *
     * @return mixed
     */
    public function bdcomPage($request, $responce, $args)
    {
        $status = false;
        $summary = 0;
        $pon = 0;
        $device = Device::where("id", "=", $args["id"])->first();
        if ($device->latency == 1) {
            $status = true;
            $summary = EponMac::where("device_id", "=", $device->id)->count();
            $pon = [
                "EPON0/1" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/1%')->count(),
                "EPON0/2" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/2%')->count(),
                "EPON0/3" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/3%')->count(),
                "EPON0/4" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/4%')->count(),
                "EPON0/5" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/5%')->count(),
                "EPON0/6" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/6%')->count(),
                "EPON0/7" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/7%')->count(),
                "EPON0/8" => EponMac::where("device_id", "=", $device->id)->where('epon', 'like', '%EPON0/8%')->count(),
            ];
        }
        return $this->view->render($responce, "bdcom.twig", ["bdcom" => [
            "bdcom_id" => $args["id"],
            "status" => $status,
            "summary" => $summary,
            "pon" => $pon,
        ]]);
    }

    /**
     * Show information about current EPON interface
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and epon interface id
     *
     * @return mixed
     */
    public function bdcomEpon($request, $responce, $args)
    {
        return $this->view->render($responce, "epon.twig", ["bdcom" => [
            "bdcom_id" => $args["id"],
            "epon_id" => $args["epon"],
        ]]);
    }

    /**
     * Show information about binding onu on EPON interface
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and epon interface id
     *
     * @return mixed
     */
    public function bdcomEponInfo($request, $responce, $args)
    {
        $orderby = $request->getParam('order')[0]["column"];
        $sort['col'] = $request->getParam('columns')[$orderby]['data'];
        $sort['dir'] = $request->getParam('order')[0]["dir"];

        $search = $request->getParam('search')['value'];

        $query = EponMac::where("device_id", "=", $args['id'])->where('epon', 'like', '%EPON0/' . $args['epon'] . '%')->where('mac', 'like', '%' . $search . '%');

        $output['recordsTotal'] = $query->count();

        $output['data'] = $query->orderBy($sort["col"], $sort["dir"])->skip($request->getParam('start'))->take($request->getParam('length', 10))->get();

        $output['recordsFiltered'] = $output['recordsTotal'];

        $output['draw'] = intval($request->getParam('draw'));

        foreach ($output["data"] as $key => $value) {
            if ($value["oper_status"] == 1) {
                $value["status"] = "<span style='color:limegreen;'><b>online</b></span>";
            }
            if ($value["oper_status"] == 2) {
                $value["status"] = "<span style='color:red;'><b>offline</b></span>";
            }
        }

        $json = json_encode($output);

        echo $json;

    }

    /**
     * Page for adding new onu
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and epon interface id
     *
     * @return mixed
     */
    public function bdcomAddEponPage($request, $responce, $args)
    {
        return $this->view->render($responce, "add_bdcom_epon.twig", ["bdcom" => [
            "bdcom_id" => $args["id"],
            "epon_id" => $args["epon"],
        ]]);
    }

    /**
     * Adding new onu
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and epon interface id
     *
     * @return mixed
     */
    public function bdcomAddEpon($request, $responce, $args)
    {
        $validation = $this->validator->validate($request, [
            'mac_part_1' => valid::noWhitespace()->notEmpty()->alnum(),
            'mac_part_2' => valid::noWhitespace()->notEmpty()->alnum(),
            'mac_part_3' => valid::noWhitespace()->notEmpty()->alnum(),
        ]);

        if ($validation->failed()) {
            return $responce->withRedirect($this->router->pathFor("epon.add", array("id" => $args["id"],"epon" => $args["epon"])));
        }

        $mac = $request->getParam("mac_part_1").".".$request->getParam("mac_part_2").".".$request->getParam("mac_part_3");

        $this->bindEponOnu($args["id"],$args["epon"],$mac);

        exec("/usr/bin/php5 /var/www/pon/scripts/EponMac.php",$output,$return);

        return $responce->withRedirect($this->router->pathFor("bdcom", array("id" => $args["id"])));

    }

    /**
     * Create script for adding onu
     *
     * @param integer $bdcom - device id
     *
     * @param integer $epon - interface epon id
     *
     * @param string $mac - mac address binding onu
     *
     * @throws \Exception
     */
    protected function bindEponOnu($bdcom,$epon,$mac)
    {
        $user = $this->auth->user()->login;
        $device = Device::where("id","=",$bdcom)->first();
        $ip = $this->ip->convertLong2IP($device->ip);

        $ssh = new SSH2($ip);
        if (!$ssh->login($user, 'Fibra.Net')) {
            exit('Login Failed');
        }
        $ssh->write("enable\n");
        echo $ssh->read();
        $ssh->write("config\n");
        echo $ssh->read();
        $ssh->write("interface EPON 0/{$epon}\n");
        echo $ssh->read();
        $ssh->write("epon bind-onu mac {$mac}\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("wr all\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("exit\n");

    }

    /**
     * Deleting onu
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and epon interface id
     *
     * @return mixed
     */
    public function bdcomDeleteOnu($request, $responce, $args)
    {
        $pon = EponMac::where("id","=",$args["onu"])->first();
        $epon = explode(":",$pon->epon);

        $this->noBindEponOnu($args['id'],$epon[0],$pon->mac);

        exec("/usr/bin/php5 /var/www/pon/scripts/EponMac.php",$output,$return);

        return $responce->withRedirect($this->router->pathFor("bdcom", array("id" => $args["id"])));
    }

    /**
     * Create script for deleting onu
     *
     * @param integer $bdcom - device id
     *
     * @param integer $epon - interface epon id
     *
     * @param string $mac - mac address binding onu
     *
     * @throws \Exception
     */
    public function noBindEponOnu($bdcom,$epon,$mac)
    {
        $user = $this->auth->user()->login;
        $device = Device::where("id","=",$bdcom)->first();
        $ip = $this->ip->convertLong2IP($device->ip);

        $ssh = new SSH2($ip);
        if (!$ssh->login($user, 'Fibra.Net')) {
            exit('Login Failed');
        }
        $ssh->write("enable\n");
        echo $ssh->read();
        $ssh->write("config\n");
        echo $ssh->read();
        $ssh->write("interface {$epon}\n");
        echo $ssh->read();
        $ssh->write("no epon bind-onu mac {$mac}\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("wr all\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("exit\n");

    }
}