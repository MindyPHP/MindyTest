<?php

/*
 * This file is part of Mindy Orm.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Q;

use Mindy\QueryBuilder\QueryBuilder;

class QOrNot extends QOr
{
    public function toSQL(QueryBuilder $queryBuilder)
    {
        return 'NOT ('.parent::toSQL($queryBuilder).')';
    }
}
