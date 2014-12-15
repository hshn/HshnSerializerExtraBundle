<?php

namespace Hshn\SerializerExtraBundle\Functional\Bundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class PostVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return 'Hshn\SerializerExtraBundle\Functional\Bundle\Entity\Post';
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object))) {
            return self::ACCESS_ABSTAIN;
        }

        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            return $token->getUsername() === 'user1';
        }

        return $vote;
    }
}
