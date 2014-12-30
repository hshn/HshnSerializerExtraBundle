<?php

namespace Hshn\SerializerExtraBundle\VichUploader;

use Hshn\SerializerExtraBundle\ContextMatcher\MatcherFactory;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class EventSubscriber implements EventSubscriberInterface
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
     * @var MatcherFactory
     */
    private $matcherFactory;

    /**
     * @var PropertyMappingFactory
     */
    private $propertyMappingFactory;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;

    /**
     * @param MatcherFactory          $matcherFactory
     * @param PropertyMappingFactory  $propertyMappingFactory
     * @param StorageInterface        $storage
     * @param ConfigurationRepository $configurationRepository
     */
    public function __construct(MatcherFactory $matcherFactory, PropertyMappingFactory $propertyMappingFactory, StorageInterface $storage, ConfigurationRepository $configurationRepository)
    {
        $this->matcherFactory = $matcherFactory;
        $this->propertyMappingFactory = $propertyMappingFactory;
        $this->storage = $storage;
        $this->configurationRepository = $configurationRepository;
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
        $attributes = $this->getAttributes($configuration, $object);

        foreach ($attributes as $attribute => $alias) {
            $visitor->addData($alias, $this->storage->resolveUri($object, $attribute, $configuration->getClass()));
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
     * @return array
     */
    private function getAttributes(Configuration $configuration, $object)
    {
        if ($attributes = $configuration->getAttributes()) {
            return $attributes;
        }

        /** @var $mappings PropertyMapping[] */
        $mappings = $this->propertyMappingFactory->fromObject($object, $configuration->getClass());
        foreach ($mappings as $mapping) {
            $attribute = $mapping->getFilePropertyName();
            $attributes[$attribute] = $attribute;
        }

        return $attributes;
    }
}
