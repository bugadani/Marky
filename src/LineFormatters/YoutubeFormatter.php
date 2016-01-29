<?php

namespace Marky\LineFormatters;

use Marky\AbstractLineFormatter;

class YoutubeFormatter extends AbstractLineFormatter
{
    private static $pattern = '<div class="youtubeWrapper"><iframe class="youtube" src="http://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe></div>';

    public function getPattern()
    {
        return '/(?<!\\\)\[youtube\]\((.+?)(?<!\\\)\)/';
    }

    public function format($matches)
    {
        return sprintf(self::$pattern, $matches[1]);
    }
}
