<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class IpField.
 */
class IpField extends CharField
{
    /**
     * @var int
     */
    public $version = 4;

    /**
     * IpField constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if (!in_array($this->version, [4, 6])) {
            throw new \LogicException('Unknown IP protocol version. Allowed 4 and 6');
        }
    }

    /**
     * @return array
     */
    public function getValidationConstraints()
    {
        return array_merge(parent::getValidationConstraints(), [
            new Assert\Ip(['version' => $this->version]),
        ]);
    }
}
