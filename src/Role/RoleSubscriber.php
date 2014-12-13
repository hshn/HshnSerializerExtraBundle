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
class RoleSubscriber implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var AttributeManager
     */
    private $roleManager;

    /**
     * @var string
     */
    private $exportTo;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AttributeManager                   $roleManager
     * @param string                        $exportTo
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, AttributeManager $roleManager, $exportTo = '_roles')
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->roleManager = $roleManager;
        $this->exportTo = $exportTo;
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        $roles = [];
        foreach ($this->getRoles($event->getType()) as $role) {
            $roles[$role] = $this->authorizationChecker->isGranted([$role], $event->getObject());
        }

        /** @var $visitor JsonSerializationVisitor */
        $visitor = $event->getContext()->getVisitor();
        $visitor->addData($this->exportTo, $roles);
    }

    /**
     * @param array $type
     *
     * @return array
     */
    private function getRoles(array $type)
    {
        try {
            return $this->roleManager->getAttributes($type['name']);
        } catch (\InvalidArgumentException $e) {
            return [];
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
