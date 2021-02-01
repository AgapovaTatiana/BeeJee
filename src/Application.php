<?php
namespace Base;

use App\Controller\Admin;

class Application
{
    private $route;
    /** @var AbstractController */
    private $controller;
    private $actionName;

    public function __construct()
    {
        $this->route = new Route();
    }

    public function run()
    {
        try{
            session_start();
            $this->addRoutes();
            $this->initController();
            $this->initAction();
            $view = new View();
            $this->controller->setView($view);
            $this->initUser();

            $content = $this->controller->{$this->actionName}();
            return $content;

        }catch (RedirectException $e){
            header("Location: ". $e->getUrl());
            die;
        }catch (RouteException $e) {
            header("HTTP/1.0 404 Not Found");
            echo $e->getMessage();
        }
    }

    private function addRoutes()
    {
         $this->route =new Route();
        /** @uses \App\Controller\Admin::loginAction()*/
        $this->route->addRoute('/html/admin/login', \App\Controller\Admin::class, 'login');
        $this->route->addRoute('/html/task', \App\Controller\Task::class, 'index');
        $this->route->addRoute('/html', \App\Controller\Task::class, 'index');
        $this->route->addRoute('/html/', \App\Controller\Task::class, 'index');
    }

    private function initController()
    {
        $controllerName = $this->route->getControllerName();
        if (!class_exists($controllerName)) {
            throw new RouteException('Cant find controller ' . $controllerName);
        }

        $this->controller = new $controllerName();
    }

    private function initAction()
    {
        $actionName = $this->route->getActionName();
        if (!method_exists($this->controller, $actionName)) {
            throw new RouteException('Action ' . $actionName . ' not found in ' . get_class($this->controller));
        }

        $this->actionName = $actionName;
    }



    private function initUser()
    {
        $id = $_SESSION['id'] ?? null;
        if ($id) {
            $user = \App\Model\Admin::getById($id);
            if ($user) {
                $this->controller->setUser($user);
            }
        }
    }


/*

*/
}