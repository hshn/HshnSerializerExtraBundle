<?php


namespace Hshn\SerializerExtraBundle\Functional\Bundle\Controller;

use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * @param mixed  $data
     * @param string $format
     *
     * @return mixed
     */
    protected function serialize($data, $format = 'json')
    {
        /** @var $serializer Serializer */
        $serializer = $this->get('serializer');

        return $serializer->serialize($data, $format);
    }
}
