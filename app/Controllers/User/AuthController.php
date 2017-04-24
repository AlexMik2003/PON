<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Users;
use Illuminate\Support\Facades\Request;
use Slim\Http\Response;
use App\Helpers\Session;

/**
 * Class AuthController  - Users authentication
 *
 * @package ApCp\Controllers\User
 */
class AuthController extends BaseController
{

    /**
     * Get authorization form
     *
     * @param Request $request
     *
     * @param Response $responce
     *
     * @return mixed
     */
    public function getSignin($request,$responce)
    {
        return $this->view->render($responce, "auth.twig");
    }

     /**
     * User authorization
     *
     * @param Request $request
     *
     * @param Response $responce
     *
     * @return mixed
     */
    public function Authorization($request,$responce)
    {
        $auth = $this->auth->attempt(
            $request->getParam("username"),
            $request->getParam("password")
        );

       if(!$auth)
        {
            $this->flash->addMessage("error", "Invalid User Name/Password Please Retype");
            return $responce->withRedirect($this->router->pathFor("signin"));
        }

        return $responce->withRedirect($this->router->pathFor("index",array("page" => 1,"sort" => "created")));
    }

    /**
     * SignOut user and delete Session
     *
     * @param Request $request
     *
     * @param Response $responce
     *
     * @return mixed
     */
    public function getSignOut($request,$responce)
    {
        Users::where("id",Session::get("id"))->first();
        Session::delete("id");
        Session::delete("user");
        return $responce->withRedirect($this->router->pathFor("signin"));
    }
}