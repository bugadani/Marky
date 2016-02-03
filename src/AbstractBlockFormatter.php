<?php

namespace Marky;

abstract class AbstractBlockFormatter
{
    /**
     * @var Markdown
     */
    private $markdown;

    public function __construct(Markdown $markdown)
    {
        $this->markdown = $markdown;
    }

    /**
     * @return Markdown
     */
    public function getFormatter()
    {
        return $this->markdown;
    }

    abstract public function format($text);
}
