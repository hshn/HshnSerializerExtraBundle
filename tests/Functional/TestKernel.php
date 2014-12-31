<?php

namespace Hshn\SerializerExtraBundle\Functional;

use Hshn\SerializerExtraBundle\Functional\Bundle\HshnSerializerExtraTestBundle;
use Hshn\SerializerExtraBundle\HshnSerializerExtraBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Vich\UploaderBundle\VichUploaderBundle;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class TestKernel extends Kernel
{
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
            new HshnSerializerExtraBundle(),
            new HshnSerializerExtraTestBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }
}
