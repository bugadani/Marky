<?php

namespace Marky\BlockFormatters;

use Marky\AbstractBlockFormatter;

class CodeBlockFormatter extends AbstractBlockFormatter
{

    public function format($text)
    {
        $codeBlockPattern = '/(?:\n\n|\A)((?:(?:[ ]{4}).*\n*)+)((?=^[ ]{0,4}\S)|$)/m';

        $formatter = $this->getFormatter();

        return preg_replace_callback(
            $codeBlockPattern,
            function ($matches) use ($formatter) {
                $text = $formatter->escape($formatter->outdent($matches[1]));
                $text = ltrim($text, "\n");
                $text = strtr(
                    rtrim($text),
                    [
                        '&' => '&amp;',
                        '<' => '&lt;',
                        '>' => '&gt;'
                    ]
                );

                return "\n\n<pre><code>{$text}\n</code></pre>\n\n";
            },
            $text
        );
    }
}
