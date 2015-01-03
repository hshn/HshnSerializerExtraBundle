<?php


namespace Hshn\SerializerExtraBundle\VichUploader\UriResolver;

use Hshn\SerializerExtraBundle\VichUploader\Configuration\File;
use Hshn\SerializerExtraBundle\VichUploader\UriResolverInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ApplyFilterResolver implements UriResolverInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @param StorageInterface     $storage
     * @param CacheManager         $cacheManager
     */
    public function __construct(StorageInterface $storage, CacheManager $cacheManager)
    {
        $this->storage = $storage;
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($object, File $file, $className = null)
    {
        if (null === $filter = $file->getFilter()) {
            throw new ResolvingUriFailedException('Could not resolve URI of the file that has no filter');
        }

        if (null === $path = $this->storage->resolvePath($object, $file->getProperty(), $className)) {
            throw new ResolvingUriFailedException(sprintf('The object "%s" has no file at property "%s"', $className ?: get_class($object), $file->getProperty()));
        }

        return $this->cacheManager->getBrowserPath($path, $filter);
    }
}
