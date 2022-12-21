<?php

namespace Asylum\Utility;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class StringTemplate
{
    public function __construct($start = '{', $end = '}')
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function render($string, $replacements = [])
    {
        if (!$replacements) {
            return;
        }

        $replacements = $this->condense($replacements);

        foreach ($replacements as $key => $replacement) {
            $string = str_replace($this->start . $key . $this->end, $replacement, $string);
        }

        return $string;
    }

    private function condense(array $data) : array
    {
        $ritit = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));

        $result = [];

        foreach ($ritit as $leafValue) {
            $keys = [];
            foreach (range(0, $ritit->getDepth()) as $depth) {
                $keys[] = $ritit->getSubIterator($depth)->key();
            }
            $result[ join('.', $keys) ] = $leafValue;
        }

        return $result;
    }
}
