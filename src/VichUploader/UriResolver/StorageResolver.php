<?php

namespace Hshn\SerializerExtraBundle\VichUploader\UriResolver;

use Hshn\SerializerExtraBundle\VichUploader\Configuration\File;
use Hshn\SerializerExtraBundle\VichUploader\UriResolverInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class StorageResolver implements UriResolverInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /*
     * @param StorageInterface $storageInterface
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($object, File $file, $className = null)
    {
        if (null === $uri = $this->storage->resolveUri($object, $file->getProperty(), $className)) {
            throw new ResolvingUriFailedException(sprintf('The object "%s" has no file at property "%s"', get_class($object), $file->getProperty()));
        }

        return $uri;
    }
}
