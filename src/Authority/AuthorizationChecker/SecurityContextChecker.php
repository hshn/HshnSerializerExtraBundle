<?php

namespace Hshn\SerializerExtraBundle\Authority\AuthorizationChecker;

use Hshn\SerializerExtraBundle\Authority\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class SecurityContextChecker implements AuthorizationCheckerInterface
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->securityContext->isGranted($attributes, $object);
    }
}
