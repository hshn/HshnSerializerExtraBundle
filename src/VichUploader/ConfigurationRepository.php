<?php

namespace Hshn\SerializerExtraBundle\VichUploader;

use Hshn\SerializerExtraBundle\ParameterBag;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ConfigurationRepository
{
    /**
     * @var ParameterBag
     */
    private $configurations;

    /**
     * @param Configuration[] $configurations
     */
    public function __construct(array $configurations)
    {
        $this->configurations = new ParameterBag();

        foreach ($configurations as $configuration) {
            $this->add($configuration);
        }
    }

    /**
     * @param Configuration $configuration
     *
     * @return $this
     */
    public function add(Configuration $configuration)
    {
        $this->configurations->set($configuration->getClass(), $configuration);

        return $this;
    }

    /**
     * @param string $class
     *
     * @return Configuration
     * @throws \Exception
     */
    public function get($class)
    {
        return $this->configurations->get($class);
    }

    /**
     * @return Configuration[]|array<string, Configuration>
     */
    public function all()
    {
        return $this->configurations->all();
    }
}
