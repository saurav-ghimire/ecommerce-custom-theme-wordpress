<?php

use AgeGate\App\I18n;
use AgeGate\Admin\Ajax;
use AgeGate\Admin\Admin;
use AgeGate\App\AgeGate;
use AgeGate\Admin\Update;
use AgeGate\Enqueue\Enqueue;
use AgeGate\Legacy\Deprecated;
use AgeGate\Legacy\Check as LegacyCheck;
use AgeGate\Routes\Rest\Check;
use AgeGate\Shortcode\Shortcode;
use AgeGate\Presentation\Template;
use AgeGate\Routes\Rest\Developer;

new Deprecated;
new Admin;
new Ajax;
new Update;

new AgeGate;
new I18n;

new Template;

new Check;
new LegacyCheck;
// new Routes\Rest\Media();
new Developer;

new Shortcode;

new Enqueue;

