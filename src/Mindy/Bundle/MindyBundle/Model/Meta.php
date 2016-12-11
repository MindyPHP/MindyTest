<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/10/16
 * Time: 15:37
 */

namespace Mindy\Bundle\MindyBundle\Model;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\Model;
use Symfony\Component\Validator\Constraints as Assert;

class Meta extends Model
{
    public static function getFields()
    {
        return [
            'domain' => [
                'class' => CharField::class,
            ],
            'title' => [
                'class' => CharField::class,
                'length' => 60,
            ],
            'url' => [
                'class' => CharField::class,
            ],
            'keywords' => [
                'class' => CharField::class,
                'length' => 60
            ],
            'canonical' => [
                'class' => CharField::class,
                'validators' => [
                    new Assert\Url()
                ]
            ],
            'description' => [
                'class' => TextField::class,
                'length' => 160
            ]
        ];
    }
}