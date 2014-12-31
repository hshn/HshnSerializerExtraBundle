<?php

namespace Hshn\SerializerExtraBundle\Functional\Bundle\Controller;

use Hshn\SerializerExtraBundle\Functional\Bundle\Entity\Post;
use Hshn\SerializerExtraBundle\Functional\Bundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class PostController extends Controller
{
    /**
     * @return Response
     */
    public function showAction()
    {
        $post = new Post('post title');
        $post->setUser(new User('user1'));

        return new Response($this->serialize($post));
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $user = new User('user1');
        $user->addPost(new Post('post 1'));
        $user->addPost(new Post('post 2'));

        return new Response($this->serialize($user));
    }
}
