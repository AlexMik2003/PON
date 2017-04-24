<?php

namespace App\Middleware;


class AuthMiddleware extends Middleware
{
    public function __invoke($request,$responce,$next)
    {
       if(!$this->container->auth->check())
       {
           return $responce->withRedirect($this->container->router->pathFor("signin"));
       }

       $responce = $next($request,$responce);
        return $responce;
    }
}