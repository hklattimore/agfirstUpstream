<?php

namespace Solarium\Support\DataFixtures;

use ReflectionException;

/**
 * This class is just a convenience wrapper around the fixture loading process.
 *
 * @author Baldur Rensch <brensch@gmail.com>
 */
class FixtureLoader
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * @var Purger
     */
    private $purger;

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @param Loader   $loader
     * @param Purger   $purger
     * @param Executor $executor
     */
    public function __construct(Loader $loader, Purger $purger, Executor $executor)
    {
        $this->loader = $loader;
        $this->purger = $purger;
        $this->executor = $executor;
    }

    /**
     * @param string $dir
     * @param bool   $append
     *
     * @return self
     *
     * @throws ReflectionException
     */
    public function loadFixturesFromDir(string $dir, bool $append = true)
    {
        if (!$append) {
            $this->purger->purge();
        }

        $this->loader->loadFromDirectory($dir);

        $this->executor->execute($this->loader->getFixtures());

        return $this;
    }
}
