<?php

namespace Marky\BlockFormatters;

use Marky\AbstractBlockFormatter;

class HorizontalRuleFormatter extends AbstractBlockFormatter
{

    public function format($text)
    {
        $hrPattern = '/^[ ]{0,2}([*_-])(?>[ ]{0,2}\1){2,}\s*$/m';

        return preg_replace($hrPattern, "<hr />\n", $text);
    }
}
