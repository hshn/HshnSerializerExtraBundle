<?php


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
    public function showAction()
    {
        $temp = tempnam(sys_get_temp_dir(), md5(__CLASS__));
        file_put_contents($temp, file_get_contents(__FILE__));

        $picture = new Picture(new UploadedFile($temp, 'PictureController.php', null, null, UPLOAD_ERR_OK, true));

        $this->getUploadHandler()->upload($picture, 'file');

        return new Response($this->serialize($picture));
    }

    /**
     * @return UploadHandler
     */
    private function getUploadHandler()
    {
        return $this->get('vich_uploader.upload_handler');
    }
}
