<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

/**
 * This class contains the critical functions
 */
abstract class mainController {

    protected $settings;
    protected $title;
    protected $endpoints;
    protected $Model;
    protected $pdo;
    private $javaScripts;
    private $styleSheets;
    private $layout;
    private $viewFile;

    public function __construct(basicSettings &$settings, $title = false) {
        $this->settings = $settings;
        $this->endpoints = array();
        $this->title = $title !== false ? $this->title : ucfirst(get_class($this));
        $this->viewFile = strtolower($settings->getMethod());
        $this->setDefaultLayout();
        $this->pdo = $this->configureDatabase();
        $this->Model = $this->setModel($this->pdo);
    }

    /**
     * To ensure that there is a default method
     */
    public abstract function index();

    private function configureDatabase() {
        $serverType = $this->settings->getServerType();
        $db = new DbConn($this->settings->getConfig()->database->servers->$serverType);
        return $db->Connect();
    }

    /**
     * Returns the available callable methods of the app
     * Index is a must have
     */
    public final function getEndpoints() {
        $this->endpoints[] = 'index';
        return $this->endpoints;
    }

    private function setModel($pdo) {
        $modelName = ucfirst(get_class($this)) . 'Model';
        $modelFile = $this->settings->getConfig()->routes->model . '/' . $modelName . '.php';
        if (is_file($modelFile)) {
            require $modelFile;
            $prepare = new ReflectionClass($modelName);
            return $prepare->newInstanceArgs(array($pdo));
        } else {
            return new Model($pdo);
        }
    }

    private function setDefaultLayout() {
        $this->layout = $this->settings->getConfig()->general->defaultLayout;
        $this->addStyleSheet('reset');
        $this->addStyleSheet('base');
        $this->addJavaScript('base');
        if (is_file('Styles/Css/' . get_class($this) . '.css')) {
            $this->addStyleSheet(get_class($this));
        }
        if (is_file('Styles/Js/' . get_class($this) . '.js')) {
            $this->addJavaScript(get_class($this));
        }
    }

    protected function addJavaScript($js) {
        $this->javaScripts[] = $this->settings->getConfig()->routes->javaScripts . '/' . $js . '.js';
    }

    protected function addStyleSheet($css, $media = 'all') {
        $this->styleSheets[] = array('route' => $this->settings->getConfig()->routes->styleSheets . '/' . $css . '.css', 'media' => $media);
    }

    public final function getTitle() {
        return (strlen($this->settings->getConfig()->general->titlePrefix) > 1 ? $this->settings->getConfig()->general->titlePrefix . ' - ' : '') . $this->title;
    }

    public final function getStyleSheets() {
        return $this->styleSheets;
    }

    public final function getJavaScripts() {
        return $this->javaScripts;
    }

    public final function getLayoutFile() {
        return $this->settings->getConfig()->routes->layouts . '/' . $this->layout . '.php';
    }

    public final function setView($viewFile) {
        $this->viewFile = $viewFile;
    }

    public final function getViewFile() {
        return $this->settings->getConfig()->routes->view . '/' . ucfirst(get_class($this)) . '/' . $this->viewFile . '.php';
    }

}
