<?php

namespace Hshn\SerializerExtraBundle\VichUploader;

use Hshn\SerializerExtraBundle\AbstractContextAwareEventSubscriber;
use Hshn\SerializerExtraBundle\ContextMatcher\MatcherFactory;
use Hshn\SerializerExtraBundle\VichUploader\Configuration\File;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class EventSubscriber extends AbstractContextAwareEventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => Events::POST_SERIALIZE, 'method' => 'onPostSerialize', 'format' => 'json'],
        ];
    }

    /**
     * @var PropertyMappingFactory
     */
    private $propertyMappingFactory;

    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;

    /**
     * @var UriResolverInterface
     */
    private $uriResolver;

    /**
     * @param MatcherFactory          $matcherFactory
     * @param PropertyMappingFactory  $propertyMappingFactory
     * @param ConfigurationRepository $configurationRepository
     * @param UriResolverInterface    $uriResolver
     */
    public function __construct(MatcherFactory $matcherFactory, PropertyMappingFactory $propertyMappingFactory, ConfigurationRepository $configurationRepository, UriResolverInterface $uriResolver)
    {
        parent::__construct($matcherFactory);

        $this->propertyMappingFactory = $propertyMappingFactory;
        $this->configurationRepository = $configurationRepository;
        $this->uriResolver = $uriResolver;
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        if (!$configuration = $this->getConfiguration($event->getType())) {
            return;
        }

        $context = $event->getContext();
        if (!$this->buildContextMatcher($configuration)->matches($context)) {
            return;
        }

        /** @var $visitor JsonSerializationVisitor */
        $visitor = $event->getVisitor();
        $object = $event->getObject();
        $files = $this->getFiles($configuration, $object);

        foreach ($files as $file) {
            try {
                $visitor->addData($file->getExportTo(), $this->uriResolver->resolve($object, $file, $configuration->getClass()));
            } catch (UriResolverException $e) {
            }
        }
    }

    /**
     * @param array $type
     *
     * @return Configuration|null
     */
    private function getConfiguration(array $type)
    {
        try {
            return $this->configurationRepository->get($type['name']);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param Configuration $configuration
     *
     * @return \Hshn\SerializerExtraBundle\ContextMatcher\MatcherInterface
     */
    private function buildContextMatcher(Configuration $configuration)
    {
        return $this->matcherFactory->depth($configuration->getMaxDepth());
    }

    /**
     * @param Configuration $configuration
     * @param object        $object
     *
     * @return File[]
     */
    private function getFiles(Configuration $configuration, $object)
    {
        if ($files = $configuration->getFiles()) {
            return $files;
        }

        /** @var $mappings PropertyMapping[] */
        $mappings = $this->propertyMappingFactory->fromObject($object, $configuration->getClass());
        foreach ($mappings as $mapping) {
            $files[] = new File($mapping->getFilePropertyName());
        }

        return $files;
    }
}
