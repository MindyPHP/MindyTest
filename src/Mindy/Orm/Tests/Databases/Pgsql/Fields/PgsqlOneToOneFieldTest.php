<?php

/*
 * This file is part of Mindy Orm.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Tests\Pgsql\Fields;

use Mindy\Orm\Tests\Fields\OneToOneFieldTest;

class PgsqlOneToOneFieldTest extends OneToOneFieldTest
{
    public $driver = 'pgsql';
}
