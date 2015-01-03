<?php


namespace Hshn\SerializerExtraBundle\VichUploader;

use Hshn\SerializerExtraBundle\VichUploader\Configuration\File;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
interface UriResolverInterface
{
    /**
     * @param object $object
     * @param File   $file
     * @param string $className
     *
     * @return string
     * @throws UriResolverException
     */
    public function resolve($object, File $file, $className = null);
}
