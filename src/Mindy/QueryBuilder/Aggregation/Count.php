<?php

/*
 * This file is part of Mindy Orm.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\QueryBuilder\Aggregation;

class Count extends Aggregation
{
    public function toSQL()
    {
        return 'COUNT('.parent::toSQL().')'.(empty($this->alias) ? '' : ' AS [['.$this->alias.']]');
    }
}
