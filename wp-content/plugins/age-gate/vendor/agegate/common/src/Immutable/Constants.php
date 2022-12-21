<?php

namespace AgeGate\Common\Immutable;

class Constants
{
    // Permissions
    public const RESTRICTIONS = 'ag_manage_restrictions';
    public const APPEARANCE = 'ag_manage_appearance';
    public const ADVANCED = 'ag_manage_advanced';
    public const MESSAGES = 'ag_manage_messaging';
    public const ACCESS = 'ag_manage_settings';
    public const CONTENT = 'ag_manage_content';
    public const TOOLS = 'ag_manage_tools';
    public const EXPORT = 'ag_export';
    public const IMPORT = 'ag_import';
    public const HARD_RESET = 'ag_hard_reset';
    public const SET_CONTENT = 'ag_manage_set_content';
    public const SET_CUSTOM_AGE = 'ag_manage_set_custom_age';

    public const TESTING = 'manage_options';

    public const META_AGE = '_age_gate-age';
    public const META_BYPASS = '_age_gate-bypass';
    public const META_RESTRICT = '_age_gate-restrict';

    public const AGE_GATE_ADMIN_PERMISSION = [
        'restriction'   => self::RESTRICTIONS,
        'appearance'    => self::APPEARANCE,
        'advanced'      => self::ADVANCED,
        'message'       => self::MESSAGES,
        'access'        => self::ACCESS,
        'content'       => self::CONTENT,
        'tools'         => self::TOOLS,
    ];

    public const AGE_GATE_PERMISSION_ARRAY = [
        'restriction'   => self::RESTRICTIONS,
        'appearance'    => self::APPEARANCE,
        'advanced'      => self::ADVANCED,
        'message'       => self::MESSAGES,
        'access'        => self::ACCESS,
        'content'       => self::CONTENT,
        'set_content'   => self::SET_CONTENT,
        'set_age'       => self::SET_CUSTOM_AGE,
        'tools'         => self::TOOLS,
        'export'        => self::EXPORT,
        'import'        => self::IMPORT,
        'reset'         => self::HARD_RESET,
    ];

    // Info
    public const PATH = AGE_GATE_PATH;
    public const VERSION = AGE_GATE_VERSION;

    // Options
    public const OPTION_RESTRICTION = 'age_gate_restrictions';
    public const OPTION_MESSAGE = 'age_gate_messages';
    public const OPTION_ACCESS = 'age_gate_access';
    public const OPTION_ADVANCED = 'age_gate_advanced';
    public const OPTION_CONTENT = 'age_gate_content';
    public const OPTION_TOOLS = 'age_gate_tools';
    public const OPTION_APPEARANCE = 'age_gate_appearance';
    public const OPTION_VERSION = 'age_gate_version';

    public const AGE_GATE_OPTIONS = [
        'restriction' => self::OPTION_RESTRICTION,
        'appearance' => self::OPTION_APPEARANCE,
        'message' => self::OPTION_MESSAGE,
        'content' => self::OPTION_CONTENT,
        'advanced' => self::OPTION_ADVANCED,
        'access' => self::OPTION_ACCESS,
        'tools' => self::OPTION_TOOLS,
    ];

    public const VIEW_PATH = AGE_GATE_PATH . 'src/Resources/views/public';
}
