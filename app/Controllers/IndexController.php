<?php

namespace App\Controllers;


/**
 * Class IndexController - main project page
 *
 * @package App\Controllers
 */
class IndexController extends BaseController
{
    public function index($request,$responce)
    {
        /**
         * if user not authorized - redirect to authorization page
         * else redirect to project main page
         */
        if(!$this->auth->check())
        {
            return $responce->withRedirect($this->router->pathFor("signin"));
        }

        return $this->view->render($responce,"index.twig");

    }

}