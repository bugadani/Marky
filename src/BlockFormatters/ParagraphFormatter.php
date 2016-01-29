<?php

namespace Marky\BlockFormatters;

use Marky\AbstractBlockFormatter;

class ParagraphFormatter extends AbstractBlockFormatter
{

    public function format($text)
    {
        $markdown = $this->getFormatter();
        $text     = $markdown->hashHTML($text);

        $text  = preg_replace(['/^\n+/', '/\n+$/'], '', $text);
        $lines = preg_split('/\n{2,}/', $text);

        $formatter = function ($line) use ($markdown) {
            if ($markdown->hasHTML($line)) {
                return $markdown->getHTML($line);
            }
            $line = $markdown->formatLine($line) . '</p>';

            return preg_replace('/^([ \t]*)/', '<p>', $line);
        };

        $lines = array_map($formatter, $lines);

        return implode("\n\n", $lines);
    }
}
