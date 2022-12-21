<?php

namespace Asylum\Utility;

class Admin
{
    public static function isBlockEditor()
    {
        $currentScreen = get_current_screen();
        return method_exists($currentScreen, 'is_block_editor') && $currentScreen->is_block_editor();
    }
}
