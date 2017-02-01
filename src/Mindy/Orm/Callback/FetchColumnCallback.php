<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Callback;

use Mindy\Orm\MetaData;
use Mindy\Orm\ModelInterface;

class FetchColumnCallback
{
    protected $model;
    protected $meta;

    public function __construct(ModelInterface $model, MetaData $meta)
    {
        $this->model = $model;
        $this->meta = $meta;
    }

    public function run($column)
    {
        if ($column === 'pk') {
            return $this->model->getPrimaryKeyName();
        } elseif ($this->meta->hasForeignField($column)) {
            return strpos($column, '_id') === false ? $column.'_id' : $column;
        } elseif (strpos($column, '_id') === false) {
            $fields = $this->meta->getManyToManyFields();
            foreach ($fields as $field) {
                if (empty($field->through) === false) {
                    $meta = MetaData::getInstance($field->through);
                    if ($meta->hasForeignField($column)) {
                        return strpos($column, '_id') === false ? $column.'_id' : $column;
                    }
                }
            }

            return $column;
        }

        return $column;
    }
}
