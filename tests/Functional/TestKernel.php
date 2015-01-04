<?php

namespace Hshn\SerializerExtraBundle\Functional;

use Hshn\SerializerExtraBundle\Functional\Bundle\HshnSerializerExtraTestBundle;
use Hshn\SerializerExtraBundle\HshnSerializerExtraBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;
use Vich\UploaderBundle\VichUploaderBundle;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class TestKernel extends Kernel
{
    /**
     * @var string
     */
    private $config;

    /**
     * @param string $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        parent::__construct('test', true);
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new JMSSerializerBundle(),
            new SecurityBundle(),
            new VichUploaderBundle(),
            new LiipImagineBundle(),
            new HshnSerializerExtraBundle(),
            new HshnSerializerExtraTestBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/'.$this->config.'.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return parent::getName().Container::camelize($this->config).str_replace('.', '', Kernel::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($config)
    {
        $this->__construct($config);
    }


}
