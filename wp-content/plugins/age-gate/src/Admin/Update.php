<?php

namespace AgeGate\Admin;

class Update
{
    public function __construct()
    {
        add_action('init', [$this, 'updateCheck'], 1);
        add_action('in_plugin_update_message-age-gate/age-gate.php', [$this, 'updateWarnings']);
    }

    public function updateCheck()
    {
        if (AGE_GATE_VERSION !== get_option('age_gate_version')) {
            \AgeGate\Update\Activate::activate();
        }
    }

    public function updateWarnings($plugin)
    {
        $current = $plugin['Version'];
        $new = $plugin['new_version'];

        $method = 'get' . $this->getMagnitude($current, $new) . 'Message';

        $devs = [
            'dev',
            'alpha',
            'beta',
            'rc',
        ];

        $version = AGE_GATE_VERSION . $current;

        $found = false;

        foreach ($devs as $str) {
            if ($found) {
                continue;
            }

            $found = stripos($version, $str) !== false;
        }

        if ($found) {
            echo $this->getDevMessage();
            return;
        }

        echo $this->$method();
    }

    private function getMagnitude($current, $new)
    {
        $current = explode('.', (string) $current);
        $new = explode('.', (string)  $new);

        if ($new[0] > $current[0]) {
            return 'Major';
        } elseif ($new[1] > $current[1]) {
            return 'Minor';
        } elseif (isset($new[2]) && isset($current[2]) && $new[2] > $current[2]) {
            return 'Patch';
        }
        return 'unknown';
    }

    private function getPatchMessage()
    {
        return '<br><br>' . __('This is a patch release of Age Gate, updating directly should not cause any issues, however do ensure you have backed up any previous version settings.', 'age-gate') . ' ';
    }

    private function getMinorMessage()
    {
        $message = '<br><br><b>' . __('WARNING', 'age-gate') . ':</b> ' . __('This is a minor release of Age Gate that could have unexpected results on your site.', 'age-gate') . ' ';
        $message .=  __('While it should be safe to update, it is advised that you test locally or on a staging site first.', 'age-gate');
        return $message;
    }

    private function getMajorMessage()
    {
        $message = '<br><br><b>' . __('WARNING', 'age-gate') . ':</b> ' . __('This is a milestone release of Age Gate that could have unexpected results on your site.', 'age-gate') . ' ';
        $message .=  __('It is advised that you do not update on a live website and test locally or on a staging site first.', 'age-gate') . '<br><br>';
        $message .=  __('The update link has been disabled just to be safe, but if you are sure you want to update you can enable the update link here: ', 'age-gate') . '<button class="button-link age-gate-enable-update" type="button">' . __('Enable update', 'age-gate') . '</button>';
        $message .= $this->disableUpdate();
        return $message;
    }

    private function getDevMessage()
    {
        $message = '<br><br><b>' . __('WARNING', 'age-gate') . ':</b> ' . __('You are updating from a development version of Age Gate, some features, settings or functionality may no longer be available. Check the release notes and proceed with caution', 'age-gate') . ' <button class="button-link age-gate-enable-update" type="button">' . __('Enable update', 'age-gate') . '</button>';
        $message .= $this->disableUpdate();

        return $message;
    }

    private function getUnknownMessage()
    {
    }

    private function disableUpdate()
    {
        return '<style>#age-gate-update .update-link {pointer-events: none;cursor: default; opacity:0.3;}</style>';
    }
}
