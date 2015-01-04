<?php


namespace Hshn\SerializerExtraBundle\VichUploader\UriResolver;

use Hshn\SerializerExtraBundle\VichUploader\Configuration\File;
use Hshn\SerializerExtraBundle\VichUploader\UriResolverException;
use Hshn\SerializerExtraBundle\VichUploader\UriResolverInterface;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ChainResolver implements UriResolverInterface
{
    /**
     * @var UriResolverInterface[]
     */
    private $resolvers;

    /**
     * @param UriResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($object, File $file, $className = null)
    {
        foreach ($this->resolvers as $resolver) {
            try {
                return $resolver->resolve($object, $file, $className);
            } catch (UriResolverException $e) {
            }
        }

        throw new ResolvingUriFailedException;
    }
}
