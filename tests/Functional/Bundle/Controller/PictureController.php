<?php


namespace Proxies\__CG__\Hshn\SerializerExtraBundle\Functional\Bundle\Entity;

class Picture extends \Hshn\SerializerExtraBundle\Functional\Bundle\Entity\Picture
{
}

namespace Hshn\SerializerExtraBundle\Functional\Bundle\Controller;

use Hshn\SerializerExtraBundle\Functional\Bundle\Entity\Picture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\UploadHandler;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class PictureController extends Controller
{
    public function pictureAction()
    {
        $picture = new Picture($this->createUploadedFile('symfony.png'));

        $this->getUploadHandler()->upload($picture, 'file');

        return new Response($this->serialize($picture));
    }

    public function pictureProxyAction()
    {
        $picture = new \Proxies\__CG__\Hshn\SerializerExtraBundle\Functional\Bundle\Entity\Picture($this->createUploadedFile('symfony.png'));

        $this->getUploadHandler()->upload($picture, 'file');

        return new Response($this->serialize($picture));
    }

    /**
     * @param string $filename
     * @param string|null $path
     *
     * @return UploadedFile
     */
    private function createUploadedFile($filename, $path = null)
    {
        if ($path === null) {
            $path = __DIR__.'/../Resources/images/' . $filename;
        }

        $temp = tempnam(sys_get_temp_dir(), md5(__CLASS__));
        file_put_contents($temp, file_get_contents($path));

        return new UploadedFile($temp, $filename, null, null, UPLOAD_ERR_OK, true);
    }

    /**
     * @return UploadHandler
     */
    private function getUploadHandler()
    {
        return $this->get('vich_uploader.upload_handler');
    }
}
