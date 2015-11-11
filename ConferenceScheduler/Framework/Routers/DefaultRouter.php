<?php

namespace Framework\Routers;

class DefaultRouter implements IRouter {
    public function getURI() {
        return isset($_GET['uri']) ? strtolower($_GET['uri']) : null;
    }

    public function getPost()
    {
        return $_POST;
    }

    public function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}