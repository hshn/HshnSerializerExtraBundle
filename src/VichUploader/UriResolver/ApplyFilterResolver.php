<?php


namespace Hshn\SerializerExtraBundle\VichUploader\UriResolver;

use Hshn\SerializerExtraBundle\VichUploader\Configuration\File;
use Hshn\SerializerExtraBundle\VichUploader\UriResolverInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ApplyFilterResolver implements UriResolverInterface
{
    /**
     * @var UriResolverInterface
     */
    private $originalResolver;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @param UriResolverInterface $originalResolver
     * @param CacheManager         $cacheManager
     */
    public function __construct(UriResolverInterface $originalResolver, CacheManager $cacheManager)
    {
        $this->originalResolver = $originalResolver;
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($object, File $file, $className = null)
    {
        $uri = $this->originalResolver->resolve($object, $file, $className);
        $filter = $file->getFilter();

        return $filter === null ? $uri : $this->cacheManager->getBrowserPath($uri, $filter);
    }
}
