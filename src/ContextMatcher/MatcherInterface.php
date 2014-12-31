<?php

namespace Hshn\SerializerExtraBundle\ContextMatcher;

use JMS\Serializer\Context;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
interface MatcherInterface
{
    /**
     * @param Context $context
     *
     * @return bool
     */
    public function matches(Context $context);
}
