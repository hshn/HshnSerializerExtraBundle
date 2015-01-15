<?php

namespace Hshn\SerializerExtraBundle\Authority;


use Hshn\SerializerExtraBundle\AbstractContextAwareEventSubscriber;
use Hshn\SerializerExtraBundle\ContextMatcher\MatcherFactory;
use Hshn\SerializerExtraBundle\Util\ClassUtils;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class EventSubscriber extends AbstractContextAwareEventSubscriber
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;

    /**
     * @var string
     */
    private $exportTo;

    /**
     * @param MatcherFactory                $matcherFactory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ConfigurationRepository       $configurationRepository
     * @param string                        $exportTo
     */
    public function __construct(MatcherFactory $matcherFactory, AuthorizationCheckerInterface $authorizationChecker, ConfigurationRepository $configurationRepository, $exportTo = '_roles')
    {
        parent::__construct($matcherFactory);

        $this->authorizationChecker = $authorizationChecker;
        $this->configurationRepository = $configurationRepository;
        $this->exportTo = $exportTo;
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

        $roles = [];
        foreach ($configuration->getAttributes() as $attribute) {
            $roles[$attribute] = $this->authorizationChecker->isGranted($attribute, $event->getObject());
        }

        if (empty($roles)) {
            return;
        }

        /** @var $visitor JsonSerializationVisitor */
        $visitor = $context->getVisitor();
        $visitor->addData($this->exportTo, $roles);
    }

    /**
     * @param array $type
     *
     * @return Configuration|null
     */
    private function getConfiguration(array $type)
    {
        try {
            return $this->configurationRepository->get(ClassUtils::getRealClass($type['name']));
        } catch (\InvalidArgumentException $e) {
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => Events::POST_SERIALIZE, 'method' => 'onPostSerialize', 'format' => 'json'],
        ];
    }
}
