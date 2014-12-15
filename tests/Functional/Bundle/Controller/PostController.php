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
        /** @var $serializer Serializer */
        $serializer = $this->get('serializer');

        $post = new Post('post title');
        $post->setUser(new User('user1'));

        return new Response($serializer->serialize($post, 'json'));
    }
}
