<?php

namespace Hshn\SerializerExtraBundle\Functional\Bundle\Controller;

use Hshn\SerializerExtraBundle\Functional\Bundle\Entity\Post;
use Hshn\SerializerExtraBundle\Functional\Bundle\Entity\User;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    /**
     * @param mixed  $data
     * @param string $format
     *
     * @return mixed
     */
    private function serialize($data, $format = 'json')
    {
        /** @var $serializer Serializer */
        $serializer = $this->get('serializer');

        return $serializer->serialize($data, $format);
    }
}
