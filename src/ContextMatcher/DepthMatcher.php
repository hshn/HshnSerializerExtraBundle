<?php


namespace Hshn\SerializerExtraBundle\ContextMatcher;

use JMS\Serializer\Context;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class DepthMatcher implements MatcherInterface
{
    /**
     * @var int
     */
    private $maxDepth;

    /**
     * @param int $maxDepth
     */
    public function __construct($maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * {@inheritdoc}
     */
    public function matches(Context $context)
    {
        return $this->maxDepth === -1 ? true
            : $context->getDepth() <= $this->maxDepth;
    }
}
