<?php


namespace Hshn\SerializerExtraBundle\Authority;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ConfigurationRepository implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $configuration;

    /**
     *
     */
    public function __construct()
    {
        $this->configuration = [];
    }

    /**
     * @param string        $class
     * @param Configuration $configuration
     */
    public function set($class, Configuration $configuration)
    {
        $this->configuration[$class] = $configuration;
    }

    /**
     * @param string $class
     *
     * @return Configuration
     */
    public function get($class)
    {
        if (!isset($this->configuration[$class])) {
            throw new \InvalidArgumentException(sprintf('Unsupported class "%s"', $class));
        }

        return $this->configuration[$class];
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return array_keys($this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->configuration);
    }
}
