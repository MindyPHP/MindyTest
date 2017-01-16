<?php
/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 *
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 05/05/14.05.2014 18:53
 */

namespace Mindy\OrmNestedSet\Tests;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\TreeModel;

class NestedModel extends TreeModel
{
    public static function getFields()
    {
        return array_merge(parent::getFields(), [
            'name' => [
                'class' => CharField::class,
            ],
        ]);
    }

    public static function t($id, array $parameters = [], $domain = null, $locale = null)
    {
        return strtr($id, $parameters);
    }
}
