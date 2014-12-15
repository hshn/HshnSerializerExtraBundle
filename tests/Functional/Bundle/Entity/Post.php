<?php

namespace Hshn\SerializerExtraBundle\Functional\Bundle\Entity;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class Post
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
