<?php


namespace Hshn\SerializerExtraBundle\Functional\Bundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 *
 * @Vich\Uploadable()
 * @Serializer\ExclusionPolicy("ALL")
 */
class Picture
{
    /**
     * @var string
     *
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var File
     *
     * @Vich\UploadableField(fileNameProperty="name", mapping="picture")
     */
    private $file;

    /**
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @param string $name
     *
     * @return Picture
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param File $file
     *
     * @return Picture
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}
