<?php

namespace AgeGate\Utility;

class Age
{
    public static function calculateAge($date)
    {
        $from = new \DateTime($date);
        $to   = new \DateTime('today');
        return $from->diff($to)->y;
    }
}
