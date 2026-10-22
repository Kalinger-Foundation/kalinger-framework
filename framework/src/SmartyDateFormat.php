<?php

namespace Kalinger;

use InvalidArgumentException;

/**
 * Smarty 5 date_format compatibility for PHP 8.1+ (where strftime is deprecated).
 * Keeps Smarty's timestamp/default-date parsing and replaces only the strftime branch.
 */
class SmartyDateFormat
{
    public static function format($value, $format = null, $defaultDate = '', $formatter = 'auto')
    {
        $smartyDateFormat = [new \Smarty\Extension\DefaultExtension(), 'smarty_modifier_date_format'];

        if ($format === null || ($formatter !== 'strftime' && strpos($format, '%') === false)) {
            return $smartyDateFormat($value, $format, $defaultDate, $formatter);
        }

        $directives = [
            'Y' => 'Y', 'y' => 'y', 'm' => 'm', 'd' => 'd',
            'H' => 'H', 'M' => 'i', 'S' => 's', '%' => '\\%',
        ];
        $phpFormat = '';
        for ($i = 0, $length = strlen($format); $i < $length; $i++) {
            $char = $format[$i];
            if ($char === '%') {
                $directive = $format[++$i] ?? null;
                if ($directive === null || !isset($directives[$directive])) {
                    throw new InvalidArgumentException('Unsupported Smarty date_format directive: %' . $directive);
                }
                $phpFormat .= $directives[$directive];
            } else {
                // date() treats unescaped letters as directives, unlike strftime().
                $phpFormat .= ctype_alpha($char) || $char === '\\' ? '\\' . $char : $char;
            }
        }

        return $smartyDateFormat($value, $phpFormat, $defaultDate, 'date');
    }
}
