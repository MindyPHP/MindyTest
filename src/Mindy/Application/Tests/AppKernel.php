<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Application\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel.
 */
class AppKernel extends Kernel
{
    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances
     */
    public function registerBundles()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->rootDir.'/runtime/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->rootDir.'/runtime/logs/'.$this->environment;
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
