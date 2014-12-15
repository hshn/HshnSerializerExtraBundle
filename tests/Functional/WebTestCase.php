<?php


namespace Hshn\SerializerExtraBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class WebTestCase extends BaseWebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass()
    {
        return 'Hshn\SerializerExtraBundle\Functional\TestKernel';
    }
}
