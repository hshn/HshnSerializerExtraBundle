<?php

namespace Hshn\SerializerExtraBundle\Role;


use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class EventSubscriber implements EventSubscriberInterface
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
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ConfigurationRepository       $configurationRepository
     * @param string                        $exportTo
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, ConfigurationRepository $configurationRepository, $exportTo = '_roles')
    {
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

        $maxDepth = $configuration->getMaxDepth();
        $context = $event->getContext();

        if (-1 !== $maxDepth && $maxDepth < $context->getDepth()) {
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
            return $this->configurationRepository->get($type['name']);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
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
