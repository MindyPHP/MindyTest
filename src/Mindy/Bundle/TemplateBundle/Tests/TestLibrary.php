<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\TemplateBundle\Tests;

use Mindy\Template\Library;

class TestLibrary extends Library
{
    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'foo' => function () {
                return 'bar';
            },
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
