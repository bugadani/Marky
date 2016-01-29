<?php

namespace Marky;

abstract class AbstractLineFormatter extends AbstractBlockFormatter
{
    public function prepare($text)
    {
        return $text;
    }

    abstract public function getPattern();
}
