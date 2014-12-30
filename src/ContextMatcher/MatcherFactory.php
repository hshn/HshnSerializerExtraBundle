<?php


namespace Hshn\SerializerExtraBundle\ContextMatcher;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class MatcherFactory
{
    /**
     * @param int $depth
     *
     * @return MatcherInterface
     */
    public function depth($depth)
    {
        return new DepthMatcher($depth);
    }

    /**
     * @param MatcherInterface[] $matcher
     *
     * @return MatcherInterface
     */
    public function logicalAnd(array $matcher)
    {
        return new AndMatcher($matcher);
    }
}
