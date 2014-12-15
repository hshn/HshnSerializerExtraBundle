<?php


namespace Hshn\SerializerExtraBundle\Functional\Bundle\Entity;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class User
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Post[]
     */
    private $posts;

    /**
     * @param string $name
     * @param array  $posts
     */
    public function __construct($name, array $posts = [])
    {
        $this->name = $name;
        foreach ($posts as $post) {
            $this->addPost($post);
        }
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function addPost(Post $post)
    {
        $this->posts[] = $post->setUser($this);

        return $this;
    }
}
