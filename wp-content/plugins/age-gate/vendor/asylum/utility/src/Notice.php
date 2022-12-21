<?php

namespace Asylum\Utility;

class Notice
{
    private const OPTION = 'age_gate_messages';

    public static function add($messages, $type = 'error')
    {
        $key = static::OPTION . '_' . md5(get_current_user_id());
        $notices = self::get($key);

        if (is_array($messages)) {
            foreach ($messages as $message) {
                $notices[] = [
                    'type' => $type,
                    'message' => sanitize_text_field(ucfirst(preg_replace('/([a-z])(?:\.)([a-zA-Z0-9])/', '$1 $2', strtolower($message)))),
                ];
            }
        } else {
            $notices[] = [
                'type' => $type,
                'message' => sanitize_text_field(ucfirst(preg_replace('/([a-z])(?:\.)([a-zA-Z0-9])/', '$1 $2', strtolower($messages)))),
            ];
        }

        set_transient($key, $notices, HOUR_IN_SECONDS);
    }

    public static function get()
    {
        $key = static::OPTION . '_' . md5(get_current_user_id());
        $messages = get_transient($key) ?: [];
        delete_transient($key );
        return $messages;
    }
}