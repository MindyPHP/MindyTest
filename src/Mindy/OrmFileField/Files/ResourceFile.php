<?php

/**
 * User: max
 * Date: 24/07/15
 * Time: 16:23.
 */

namespace Mindy\Orm\Files;

/**
 * Class ResourceFile.
 */
class ResourceFile extends File
{
    /**
     * ResourceFile constructor.
     *
     * @param string      $content
     * @param null|string $name
     * @param null|string $tempDir
     */
    public function __construct($content, $name = null, $tempDir = null)
    {
        $path = tempnam($tempDir ?: sys_get_temp_dir(), 'tmp');

        if ($name) {
            $path = dirname($path).DIRECTORY_SEPARATOR.$name;
        }

        file_put_contents($path, $content);

        parent::__construct($path);
    }
}
