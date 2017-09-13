<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Util;

/**
 * The file size formatter
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FileSizeFormatter
{
    /**
     * @param int $size
     * @param int $precision
     *
     * @return string
     */
    public static function formatBytes(int $size, int $precision = 2) : string
    {
        if ($size === 0) {
            return '0B';
        }

        $base     = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[(int)floor($base)];
    }
}
