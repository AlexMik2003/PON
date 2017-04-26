<?php

namespace App\Controllers\RAISECOM;

use App\Controllers\BaseController;
use App\Models\Device;
use App\Models\GponOnu;
use App\Models\GponSn;
use \Respect\Validation\Validator as valid;
use phpseclib\Net\SSH2;

class RaisecomController extends BaseController
{
    /**
     * Show summary information about GPON interface
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and gpon interface id
     *
     * @return mixed
     */
    public function raisecomPage($request, $responce, $args)
    {
        $status = false;
        $summary = 0;
        $pon = 0;
        $device = Device::where("id", "=", $args["id"])->first();
        if ($device->latency == 1) {
            $status = true;
            $summary = GponSn::where("device_id", "=", $device->id)->count();
            $pon = [
                "GPON-OLT 1/1" => GponSn::where("device_id", "=", $device->id)->where('gpon', 'like', '%1/1%')->count(),
                "GPON-OLT 1/2" => GponSn::where("device_id", "=", $device->id)->where('gpon', 'like', '%1/2%')->count(),
                "GPON-OLT 1/3" => GponSn::where("device_id", "=", $device->id)->where('gpon', 'like', '%1/3%')->count(),
                "GPON-OLT 1/4" => GponSn::where("device_id", "=", $device->id)->where('gpon', 'like', '%1/4%')->count(),
            ];
        }

       return $this->view->render($responce, "raisecom.twig", ["raisecom" => [
           "raisecom_id" => $args["id"],
           "status" => $status,
            "summary" => $summary,
            "pon" => $pon,
        ]]);
    }

    /**
     * Show information about current GPON interface
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and gpon interface id
     *
     * @return mixed
     */
    public function raisecomGpon($request, $responce, $args)
    {
        return $this->view->render($responce, "gpon.twig", ["raisecom" => [
            "raisecom_id" => $args["id"],
            "gpon_id" => $args["gpon"],
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
    public function raisecomGponInfo($request, $responce, $args)
    {
        $orderby = $request->getParam('order')[0]["column"];
        $sort['col'] = $request->getParam('columns')[$orderby]['data'];
        $sort['dir'] = $request->getParam('order')[0]["dir"];

        $search = $request->getParam('search')['value'];

        $query = GponSn::where("device_id", "=", $args['id'])->where('gpon', 'like', '%1/' . $args['gpon'] . '%')->where('sn', 'like', '%' . $search . '%');

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
     * @param array $args - device id and gpon interface id
     *
     * @return mixed
     */
    public function raisecomAddGponPage($request, $responce, $args)
    {
        return $this->view->render($responce, "add_raisecom_gpon.twig", ["raisecom" => [
            "raisecom_id" => $args["id"],
            "gpon_id" => $args["gpon"],
        ]]);
    }

    /**
     * Adding new onu
     *
     * @param Request $request
     *
     * @param Responce $responce
     *
     * @param array $args - device id and gpon interface id
     *
     * @return mixed
     */
    public function raisecomAddGpon($request, $responce, $args)
    {
        $validation = $this->validator->validate($request, [
            'raisecom_sn' => valid::noWhitespace()->notEmpty()->alnum(),
        ]);

        if ($validation->failed()) {
            return $responce->withRedirect($this->router->pathFor("gpon.add", array("id" => $args["id"],"gpon" => $args["gpon"])));
        }

        $id = 6;

        if($request->getParam("real"))
        {
            $id = 2;
        }

        $this->container["db"]->table("gpon_onu")->truncate();
        GponOnu::create([
            "sn" => $request->getParam("raisecom_sn"),
        ]);

        $this->createGponOnu($args["id"],$args["gpon"],$request->getParam("raisecom_sn"),$id);

        exec("/usr/bin/php5 /var/www/pon/scripts/GponSn.php",$output,$return);

        return $responce->withRedirect($this->router->pathFor("raisecom", array("id" => $args["id"])));

    }

    /**
     * Create script for adding onu
     *
     * @param integer $raisecom - device id
     *
     * @param integer $epon - interface gpon id
     *
     * @param string $mac - sn for create
     *
     * @throws \Exception
     */
    public function createGponOnu($raisecom,$gpon,$rsn,$id)
    {
        $user = $this->auth->user()->login;
        $device = Device::where("id","=",$raisecom)->first();
        $ip = $this->ip->convertLong2IP($device->ip);

        $ssh = new SSH2($ip);
        if (!$ssh->login($user, 'Fibra.Net')) {
            exit('Login Failed');
        }
        $ssh->write("{$user}\n");
        echo $ssh->read();
        $ssh->write("Fibra.Net\n");
        echo $ssh->read();
        $ssh->write("enable\n");
        echo $ssh->read();
        $ssh->write("dfvgbh99\n");
        echo $ssh->read();
        $ssh->write("config\n");
        echo $ssh->read();
        $ssh->write("interface gpon-olt 1/{$gpon}\n");
        echo $ssh->read();
        $ssh->write("create gpon-onu sn {$rsn} line-profile-id {$id} service-profile-id {$id}\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("wr startup-config\n");
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
     * @param array $args - device id and gpon interface id
     *
     * @return mixed
     */
    public function raisecomDeleteOnu($request, $responce, $args)
    {
        $pon = GponSn::where("id","=",$args["onu"])->first();
        $gpon = explode("/",$pon->gpon);

        $this->noCreateGponOnu($args['id'],$gpon[1],$gpon[2]);

        exec("/usr/bin/php5 /var/www/pon/scripts/GponSn.php",$output,$return);

        return $responce->withRedirect($this->router->pathFor("raisecom", array("id" => $args["id"])));
    }

    /**
     * Create script for deleting onu
     *
     * @param integer $raisecom - device id
     *
     * @param integer $gpon - interface gpon id
     *
     * @param string $onu - onu number
     *
     * @throws \Exception
     */
    public function noCreateGponOnu($raisecom,$gpon,$onu)
    {
        $user = $this->auth->user()->login;
        $device = Device::where("id","=",$raisecom)->first();
        $ip = $this->ip->convertLong2IP($device->ip);

        $ssh = new SSH2($ip);
        if (!$ssh->login($user, 'Fibra.Net')) {
            exit('Login Failed');
        }
        $ssh->write("{$user}\n");
        echo $ssh->read();
        $ssh->write("Fibra.Net\n");
        echo $ssh->read();
        $ssh->write("enable\n");
        echo $ssh->read();
        $ssh->write("dfvgbh99\n");
        echo $ssh->read();
        $ssh->write("config\n");
        echo $ssh->read();
        $ssh->write("interface gpon-olt 1/{$gpon}\n");
        echo $ssh->read();
        $ssh->write("no create gpon-onu {$onu}\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("wr startup-config\n");
        echo $ssh->read();
        $ssh->write("exit\n");
        echo $ssh->read();
        $ssh->write("exit\n");


    }

}