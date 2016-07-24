<?php

namespace Mindy\Orm;

use Mindy\Exception\Exception;
use Mindy\Helper\Creator;
use Mindy\Query\ConnectionManager;

/**
 * Class ManyToManyManager
 * @package Mindy\Orm
 */
class ManyToManyManager extends RelatedManager
{
    /**
     * Link table name
     * @var string
     */
    public $relatedTable;
    /**
     * @var string
     */
    public $relatedTableAlias;
    /**
     * Main model
     * @var \Mindy\Orm\Model
     */
    public $primaryModel;
    /**
     * @var string
     */
    public $primaryModelColumn;
    /**
     * @var string
     */
    public $modelColumn;
    /**
     * @var null|string
     */
    public $through;
    /**
     * @var array
     */
    public $throughLink = [];
    /**
     * @var array extra condition for join
     */
    public $extra = [];

    public function __construct(Model $model, array $config = [])
    {
        Creator::configure($this, $config);
        $this->_model = $model;
    }

    public function clean()
    {
        if ($this->primaryModel->pk === null) {
            throw new Exception('Unable to clean models: the primary key of ' . get_class($this->primaryModel) . ' is null.');
        }

        $db = $this->primaryModel->getDb();

        /** @var $command \Mindy\Query\Command */
        $command = $db->createCommand()->delete($this->relatedTable, [
            $this->primaryModelColumn => $this->primaryModel->pk,
        ]);

        return $command->execute();
    }

    public function link(Model $model, array $extra = [])
    {
        return $this->linkUnlinkProcess($model, true, $extra);
    }

    public function unlink(Model $model)
    {
        return $this->linkUnlinkProcess($model, false);
    }

    protected function linkUnlinkProcess(Model $model, $link = true, array $extra = [])
    {
        if ($this->primaryModel->pk === null) {
            throw new Exception('Unable to unlink models: the primary key of ' . get_class($this->primaryModel) . ' is null.');
        }

        $method = $link ? 'insert' : 'delete';

        if ($this->primaryModel->pk === null) {
            throw new Exception('Unable to ' . ($link ? 'link' : 'unlink') . ' models: the primary key of ' . get_class($this->primaryModel) . ' is null.');
        }

        if ($this->through && $link) {
            $throughModel = new $this->through;
            if (empty($this->throughLink)) {
                throw new Exception("throughLink is missing in ManyToManyManager");
            }
            if (empty($this->throughLink)) {
                $fromId = $this->primaryModelColumn;
                $toId = $this->modelColumn;
            } else {
                list($fromId, $toId) = $this->throughLink;
            }
            list($through, $created) = $throughModel->objects()->using($this->primaryModel->getDb())->getOrCreate([
                $fromId => $this->primaryModel->pk,
                $toId => $model->pk,
            ]);
            return $through->pk;
        } else {
            $db = $this->primaryModel->getDb();
            /** @var $command \Mindy\Query\Command */
            $command = $db->createCommand()->$method($this->relatedTable, array_merge([
                $this->primaryModelColumn => $this->primaryModel->pk,
                $this->modelColumn => $model->pk,
            ], $extra));

            return $command->execute();
        }
    }
}
