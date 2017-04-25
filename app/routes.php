<?php

$app->get("/", "index:index")->setName("index");

$app->get("/signin","authorized:getSignin")->setName("signin");
$app->post("/signin", "authorized:Authorization");

$app->group('', function () {
    $this->get("/signout", "authorized:getSignOut")->setName("signout");

    $this->group("/bdcom/{id}",function (){
        $this->get("/summary", "bdcom:bdcomPage")->setName("bdcom");
        $this->get("/epon/{epon}", "bdcom:bdcomEpon")->setName("epon");
        $this->get("/epon/{epon}/json", "bdcom:bdcomEponInfo");
        $this->get("/epon/{epon}/add", "bdcom:bdcomAddEponPage")->setName("epon.add");
        $this->post("/epon/{epon}/add", "bdcom:bdcomAddEpon");
        $this->get("/epon/delete/{onu}", "bdcom:bdcomDeleteOnu");
    });

    $this->group("/raisecom/{id}",function (){
        $this->get("/summary", "raisecom:raisecomPage")->setName("raisecom");
        $this->get("/gpon/{gpon}", "raisecom:raisecomGpon")->setName("gpon");
        $this->get("/gpon/{gpon}/json", "raisecom:raisecomGponInfo");
        $this->get("/gpon/{gpon}/add", "raisecom:raisecomAddGponPage")->setName("gpon.add");
        $this->post("/gpon/{gpon}/add", "raisecom:raisecomAddGpon");
        $this->get("/gpon/delete/{onu}", "raisecom:raisecomDeleteOnu");
    });

})->add(new \App\Middleware\AuthMiddleware($container));

