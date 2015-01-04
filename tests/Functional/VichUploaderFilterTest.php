<?php


namespace Hshn\SerializerExtraBundle\Functional;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class VichUploaderFilterTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public static function getConfiguration()
    {
        return 'vich_uploader_filter';
    }

    public function test()
    {
        $client = $this->createClient();

        /** @var $cacheManager CacheManager */
        $cacheManager = $client->getContainer()->get('liip_imagine.cache.manager');
        $cacheManager->remove();

        $client->request('GET', '/picture/show');

        $response = $client->getResponse();
        $this->assertEquals($response::HTTP_OK, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertJson($content);

        $content = json_decode($content, true);
        $this->assertArrayHasKey('name', $content);
        $this->assertArrayHasKey('file_url', $content);
        $this->assertArrayHasKey('thumb_url', $content);
        $this->assertEquals('/images/symfony.png', $content['file_url']);

        // generating a thumbnail
        $client->request('GET', $content['thumb_url']);

        $response = $client->getResponse();

        // should redirect to generated
        $this->assertEquals($response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertContains('thumb/images/symfony.png', $response->headers->get('Location'));
    }
}
