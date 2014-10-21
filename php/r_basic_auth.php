<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/Slim/Middleware/HttpBasicAuth.php';

class HttpBasicAuthCustom extends \Slim\Middleware\HttpBasicAuth
{
    protected $route;

    public function __construct($username, $password, $realm = 'Protected Area', $route = '')
    {
        $this->route = $route;
        parent::__construct($username, $password, $realm);
    }

    public function call()
    {
        if (strpos($this->app->request()->getPathInfo(), $this->route) !== false) {
            parent::call();
            return;
        }
        $this->next->call();
    }
}