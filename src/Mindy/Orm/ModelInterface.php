<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/09/16
 * Time: 16:32
 */

namespace Mindy\Orm;
use Doctrine\DBAL\Connection;
use Mindy\Form\FieldInterface;

/**
 * Interface ModelInterface
 * @package Mindy\Orm
 * @property int|string $pk
 * @method static \Mindy\Orm\Manager|\Mindy\Orm\TreeManager objects($instance = null)
 */
interface ModelInterface
{
    /**
     * @return array
     */
    public function getAttributes() : array;

    /**
     * @param null $instance
     * @return Manager
     */
    public static function objectsManager($instance = null);

    /**
     * @param bool $value
     */
    public function setIsNewRecord(bool $value);

    /**
     * @return bool
     */
    public function getIsNewRecord() : bool;

    /**
     * @return MetaData
     */
    public static function getMeta();

    /**
     * @return array
     */
    public static function getFields();

    /**
     * @return bool
     */
    public function isValid() : bool;

    /**
     * @param bool $asArray
     * @return array|string
     */
    public static function getPrimaryKeyName($asArray = false);

    /**
     * @param array $fields
     * @return bool
     */
    public function insert(array $fields = []) : bool;

    /**
     * @param array $fields
     * @return bool
     */
    public function update(array $fields = []) : bool;

    /**
     * @param array $fields
     * @return bool
     */
    public function save(array $fields = []) : bool;

    /**
     * @param array $row
     * @return ModelInterface
     */
    public static function create(array $row);

    /**
     * @return string
     */
    public static function tableName() : string;

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes);

    /**
     * @param string $name
     * @param $value
     */
    public function setAttribute(string $name, $value);

    /**
     * @param string $name
     * @param bool $throw
     * @return null|FieldInterface
     */
    public function getField(string $name, $throw = false);

    /**
     * @return Connection
     */
    public function getConnection() : Connection;

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection);
}