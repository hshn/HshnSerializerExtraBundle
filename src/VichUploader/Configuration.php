<?php

namespace Hshn\SerializerExtraBundle\VichUploader;

use Hshn\SerializerExtraBundle\VichUploader\Configuration\File;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class Configuration
{
    /**
     * @var int
     */
    private $maxDepth;

    /**
     * @var string
     */
    private $class;

    /**
     * @var File[]
     */
    private $files;

    /**
     * @param string $class
     * @param array  $files
     * @param int    $maxDepth
     */
    public function __construct($class, array $files = [], $maxDepth = -1)
    {
        $this->class = $class;
        $this->files = [];
        $this->maxDepth = $maxDepth;

        $this->addFiles($files);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param array $files
     *
     * @return $this
     */
    public function addFiles(array $files)
    {
        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * @param File $file
     *
     * @return $this
     */
    public function addFile(File $file)
    {
        foreach ($this->files as $f) {
            if ($f->getExportTo() === $file->getExportTo()) {
                throw new \InvalidArgumentException(sprintf('File destination "%s" has already been specified by others', $f->getExportTo()));
            }
        }

        $this->files[] = $file;

        return $this;
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }
}
