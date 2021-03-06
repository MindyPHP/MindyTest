<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Tests;

use Mindy\Orm\Model;

class FileModel extends Model
{
    public static function getFields()
    {
        return [];
    }

    public static function getBundleName()
    {
        return 'foo';
    }
}
