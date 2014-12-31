<?php


namespace Hshn\SerializerExtraBundle\Authority\AuthorizationChecker;

use Hshn\SerializerExtraBundle\Authority\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as BaseAuthorizationCheckerInterface;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class AuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var BaseAuthorizationCheckerInterface
     */
    private $delegate;

    /**
     * @param BaseAuthorizationCheckerInterface $delegate
     */
    public function __construct(BaseAuthorizationCheckerInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->delegate->isGranted($attributes, $object);
    }
}
