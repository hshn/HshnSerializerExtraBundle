<?php

namespace Hshn\SerializerExtraBundle\Util;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ClassUtils
{
    /**
     * @param string $class
     *
     * @return string
     */
    public static function getRealClass($class)
    {
        if (class_exists('\Doctrine\Common\Util\ClassUtils')) {
            return \Doctrine\Common\Util\ClassUtils::getRealClass($class);
        }

        return $class;
    }
}
