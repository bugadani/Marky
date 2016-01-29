<?php

namespace Marky\LineFormatters;

use Marky\AbstractLineFormatter;

class YoutubeFormatter extends AbstractLineFormatter
{

    public function getPattern()
    {
        return '/(?<!\\\)\[youtube\]\((.+?)(?<!\\\)\)/';
    }

    public function format($matches)
    {
        $pattern = '<div class="youtubeWrapper"><iframe class="youtube" src="http://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe></div>';

        return sprintf($pattern, $matches[1]);
    }
}
