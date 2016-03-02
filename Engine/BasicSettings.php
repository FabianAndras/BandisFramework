<?php

/**
 * This prepares the system
 */
class BasicSettings {

    private $menuStructure;
    private $config;
    private $serverType;

    public function __construct() {
        $this->menuStructure = $this->fetchMenu(explode('/', (isset($_GET['menu']) ? $_GET['menu'] : '')));
        $this->config = $this->loadConfig('config.json');
        $this->serverType = $this->checkServer();
    }

    private function checkServer() {
        $serverName = $_SERVER['HTTP_HOST'];
        foreach ($this->config->servers as $serverType => $server) {
            if (strpos($server, $serverName) !== false) {
                return $serverType;
            }
        }
        die('Uh-oh...something terrible happened...<br />Config mismatch');
    }

    public function getServerType() {
        return $this->serverType;
    }

    public function getController() {
        $controller = is_file($this->controllerFileFromName($this->menuStructure->controller)) ? $this->menuStructure->controller : 'home';
        return ucfirst($controller);
    }

    public function getMethod() {
        return $this->menuStructure->method;
    }

    public function controllerFileFromName($name) {
        return 'Controller/' . $name . '.php';
    }

    private function fetchMenu($url) {
        $prep = array(
            'controller' => 'Home',
            'method' => 'index'
        );
        if (isset($url[0]) && strlen($url[0]) > 3) {
            $prep['controller'] = ucfirst($url[0]);
        }
        if (isset($url[1]) && strlen($url[1]) > 3) {
            $prep['method'] = strtolower($url[1]);
        }
        $menuArr = $this->arrToObj($prep);
        $menuArr->params = array();
        if (isset($url[2])) {
            for ($i = 2; $i < count($url); $i++) {
                $menuArr->params[] = $url[$i];
            }
        }
        return $menuArr;
    }

    public function arrToObj($array) {
        return json_decode(json_encode($array), false);
    }

    public function objectify($array) {
        return $this->arrToObj($array);
    }

    private function loadConfig($cfgFile) {
        return $this->arrToObj(json_decode(file_get_contents($cfgFile), false));
    }

    public function getConfig() {
        return $this->config;
    }

}
