<?php

namespace Kalinger;

use Smarty\Extension\Base;

/** Override Smarty 5's built-in strftime-based date_format before default extensions. */
class SmartyDateFormatExtension extends Base
{
    public function getModifierCallback(string $modifierName)
    {
        return $modifierName === 'date_format' ? [SmartyDateFormat::class, 'format'] : null;
    }
}
