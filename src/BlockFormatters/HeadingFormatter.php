<?php

namespace Marky\BlockFormatters;

use Marky\AbstractBlockFormatter;

class HeadingFormatter extends AbstractBlockFormatter
{
    /**
     * @see http://www.shauninman.com/archive/2006/08/22/widont_wordpress_plugin
     *
     * @param $string
     *
     * @return string
     */
    private function widont($string)
    {
        $string = rtrim($string);

        $space = strrpos($string, ' ');
        if ($space === false) {
            return $string;
        }

        return substr($string, 0, $space) . '&nbsp;' . substr($string, $space + 1);
    }

    private function callbackHeader($str, $level)
    {
        $line = $this->getFormatter()->formatLine(
            $this->widont($str)
        );

        return "<h{$level}>{$line}</h{$level}>\n\n";
    }

    public function format($text)
    {
        $text = preg_replace_callback(
            '/^(#{1,6})\s*(.+?)\s*#*\n+/m',
            function ($matches) {
                return $this->callbackHeader($matches[2], strlen($matches[1]));
            },
            $text
        );

        return preg_replace_callback(
            '/^(.+?)[ ]*\n(=|-)(\2*)[ ]*\n+/m',
            function ($matches) {
                $levels = [
                    '=' => 1,
                    '-' => 2
                ];

                return $this->callbackHeader($matches[1], $levels[ $matches[2] ]);
            },
            $text
        );
    }
}
