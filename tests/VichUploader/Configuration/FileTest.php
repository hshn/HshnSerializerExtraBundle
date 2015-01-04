<?php


namespace Hshn\SerializerExtraBundle\VichUploader\Configuration;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function test()
    {
        $file = new File('property');
        $this->assertEquals('property', $file->getProperty());
        $this->assertEquals('property', $file->getExportTo());
        $this->assertEquals(null, $file->getFilter());
    }

    /**
     * @test
     */
    public function testExportTo()
    {
        $file = new File('property', 'export_to');
        $this->assertEquals('property', $file->getProperty());
        $this->assertEquals('export_to', $file->getExportTo());
    }

    /**
     * @test
     */
    public function testFilter()
    {
        $file = new File('property', null, 'filter');
        $this->assertEquals('filter', $file->getFilter());
    }
}
