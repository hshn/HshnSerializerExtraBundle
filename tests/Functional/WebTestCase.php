<?php


namespace Hshn\SerializerExtraBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = array())
    {
        return new TestKernel(static::getConfiguration());
    }

    /**
     * @return string
     */
    public static function getConfiguration()
    {
        throw new \LogicException(sprintf('Override "%s"', __METHOD__));
    }
}
