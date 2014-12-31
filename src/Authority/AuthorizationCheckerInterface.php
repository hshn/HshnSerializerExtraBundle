<?php


namespace Hshn\SerializerExtraBundle\Authority;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
interface AuthorizationCheckerInterface
{
    /**
     * @param mixed $attributes
     * @param mixed $object
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null);
}
