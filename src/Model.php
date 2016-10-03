<?php

namespace Mindy\Orm;

use function Mindy\app;
use Mindy\Base\Application;
use Mindy\Form\FormModelInterface;
use function Mindy\trans;
use ReflectionClass;

/**
 * Class Model
 * @package Mindy\Orm
 */
class Model extends NewOrm implements FormModelInterface
{
    /**
     * @return string
     */
    public function getVerboseName() : string
    {
        return $this->classNameShort();
    }

    /**
     * @return string
     */
    public function classNameShort() : string
    {
        $classMap = explode('\\', get_called_class());
        return end($classMap);
    }

    /**
     * @return string
     */
    public static function tableName() : string
    {
        if (defined('MINDY_ORM_TEST') && MINDY_ORM_TEST) {
            return parent::tableName();
        } else {
            return sprintf("%s_%s", self::normalizeTableName(self::getModuleName()), parent::tableName());
        }
    }

    /**
     * Return module name
     * @return string
     */
    public static function getModuleName()
    {
        $object = new ReflectionClass(get_called_class());
        $shortPath = substr($object->getFileName(), strpos($object->getFileName(), 'Modules') + 8);
        return substr($shortPath, 0, strpos($shortPath, '/'));
    }

    /**
     * @return \Mindy\Module\ModuleInterface
     */
    public static function getModule()
    {
        if ($app = app()) {
            if (($name = self::getModuleName()) && $app->hasModule($name)) {
                return $app->getModule(self::getModuleName());
            }
        }

        return null;
    }
 
    public function reverse($route, array $data = [])
    {
        return app()->router->generate($route, $data);
    }

    public static function t($id, array $parameters = [], $locale = null)
    {
        return trans(sprintf('modules.%s.main', self::getModuleName()), $id, $parameters, $locale);
    }

    public function __toString()
    {
        return (string)$this->classNameShort();
    }
}
