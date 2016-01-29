<?php

namespace Marky\BlockFormatters;

use Marky\AbstractBlockFormatter;

class BlockQuoteFormatter extends AbstractBlockFormatter
{

    public function format($text)
    {
        $formatter = $this->getFormatter();

        return preg_replace_callback(
            '/((^[ ]*>[ ]?.+\n(.+\n)*(?:\n)*)+)/m',
            function ($matches) use ($formatter) {

                // trim one level of quoting and empty lines
                $text = preg_replace('/^[ ]*>[ ]?/m', '', $matches[1]);

                // recursion to catch e.g. nested quotes
                $text = $formatter->formatBlock($text);
                $text = $formatter->hashHTML($text);

                return "<blockquote>\n{$text}\n</blockquote>\n\n";
            },
            $text
        );
    }
}
