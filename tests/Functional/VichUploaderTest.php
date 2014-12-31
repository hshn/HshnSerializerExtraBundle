<?php


namespace Hshn\SerializerExtraBundle\Functional;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class VichUploaderTest extends WebTestCase
{
    public function test()
    {
        $client = $this->createClient();
        $client->request('GET', '/picture/show');

        $response = $client->getResponse();
        $this->assertEquals($response::HTTP_OK, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertJson($content);

        $content = json_decode($content, true);
        $this->assertArrayHasKey('name', $content);
        $this->assertArrayHasKey('file_url', $content);
        $this->assertRegExp('/\/images\/[^\/]+\.php/', $content['file_url']);
    }
}
