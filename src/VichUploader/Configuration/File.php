<?php


namespace Hshn\SerializerExtraBundle\VichUploader\Configuration;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class File
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $exportTo;

    /**
     * @var null|string
     */
    private $filter;

    /**
     * @param string $property
     * @param string $exportTo
     * @param string $filter
     */
    public function __construct($property, $exportTo = null, $filter = null)
    {
        $this->property = $property;
        $this->exportTo = $exportTo ?: $property;
        $this->filter = $filter;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getExportTo()
    {
        return $this->exportTo;
    }

    /**
     * @return null|string
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
