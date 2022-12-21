<?php

namespace AgeGate\Common;

abstract class Integration
{
    abstract public function exists();

    abstract public function init();
}
