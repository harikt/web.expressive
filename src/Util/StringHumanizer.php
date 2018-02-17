<?php

namespace Dms\Web\Expressive\Util;

/**
 * The string humanizer helper class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StringHumanizer
{
    /**
     * @param  string $input
     * @return string
     */
    public static function humanize(string $input) : string
    {
        return strtr(
            $input,
            [
            '.' => ' ',
            '-' => ' ',
            '_' => ' ',
            ]
        );
    }

    /**
     * @param  string $input
     * @return string
     */
    public static function title(string $input) : string
    {
        return ucwords(self::humanize($input));
    }
}
