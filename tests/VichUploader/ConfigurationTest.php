<?php


namespace Hshn\SerializerExtraBundle\VichUploader;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage "b" has already
     */
    public function testThrowExceptionWhenAttemptToAddDuplicatedDestinationFile()
    {
        $config = new Configuration('Foo', [
            $this->getFile('a'),
            $this->getFile('b'),
            $this->getFile('c'),
        ]);

        $this->assertCount(3, $config->getFiles());
        $config->addFile($this->getFile('b'));
    }

    /**
     * @test
     */
    public function testAddFile()
    {
        $config = new Configuration('Foo');
        $this->assertCount(0, $config->getFiles());

        $config->addFile($fileA = $this->getFile('a'));
        $this->assertCount(1, $config->getFiles());

        $config->addFile($fileB = $this->getFile('b'));
        $this->assertCount(2, $config->getFiles());

        $this->assertContains($fileA, $config->getFiles());
        $this->assertContains($fileB, $config->getFiles());
    }

    /**
     * @param $exportTo
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFile($exportTo)
    {
        $file = $this->getMockBuilder('Hshn\SerializerExtraBundle\VichUploader\Configuration\File')->disableOriginalConstructor()->getMock();

        $file
            ->expects($this->any())
            ->method('getExportTo')
            ->will($this->returnValue($exportTo));

        return $file;
    }
}
