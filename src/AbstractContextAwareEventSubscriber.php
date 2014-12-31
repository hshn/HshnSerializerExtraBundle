<?php


namespace Hshn\SerializerExtraBundle;

use Hshn\SerializerExtraBundle\ContextMatcher\MatcherFactory;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
abstract class AbstractContextAwareEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var MatcherFactory
     */
    protected $matcherFactory;

    /**
     * @param MatcherFactory $matcherFactory
     */
    public function __construct(MatcherFactory $matcherFactory)
    {
        $this->matcherFactory = $matcherFactory;
    }
}
