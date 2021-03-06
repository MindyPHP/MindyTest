<?php

/*
 * This file is part of Mindy Orm.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Tests\Databases\Pgsql;

use Mindy\Orm\Tests\Sync\SyncTest;

class PgsqlSyncTest extends SyncTest
{
    public $driver = 'pgsql';
}
