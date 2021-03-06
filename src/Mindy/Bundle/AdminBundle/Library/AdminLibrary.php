<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Library;

use Mindy\Bundle\AdminBundle\Admin\AdminMenu;
use Mindy\Template\Library;
use Mindy\Template\Renderer;

class AdminLibrary extends Library
{
    protected $adminMenu;
    protected $renderer;

    public function __construct(AdminMenu $adminMenu, Renderer $renderer)
    {
        $this->adminMenu = $adminMenu;
        $this->renderer = $renderer;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'admin_menu' => function ($template = 'admin/_menu.html') {
                return $this->renderer->render($template, [
                    'adminMenu' => $this->adminMenu->getMenu(),
                ]);
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
