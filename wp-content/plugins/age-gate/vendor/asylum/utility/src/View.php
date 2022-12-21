<?php

namespace Asylum\Utility;

use League\Plates\Engine;

class View
{
    private $templates;

    public function __construct($default = null)
    {
        $this->init($default);
    }

    public function init($default = null)
    {
        $this->templates = new Engine($default);
        return $this;
    }

    public function addFolder(string $name, string $path) : View
    {
        $this->templates->addFolder($name, $path, true);
        return $this;
    }

    public function addData(array $data) : View
    {
        $this->templates->addData($data);
        return $this;
    }

    public function addExtension($class)
    {
        $this->templates->loadExtension($class);
        return $this;
    }

    public function addFunction($name, $callback)
    {
        $this->templates->registerFunction($name, $callback);
        return $this;
    }

    public function compile(string $view, array $data = []) : string
    {
        return $this->templates->render($view, $data);
    }

    public function render(string $view, array $data = []) : void
    {
        echo $this->compile($view, $data);
    }
}
