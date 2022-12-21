<?php

namespace AgeGate\Common\Interfaces;

interface ControllerInterface
{
    public function init() : void;

    public function assets() : void;
}
