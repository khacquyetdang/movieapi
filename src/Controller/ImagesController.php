<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class ImagesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var string
     */
    private $imageDirectory;

    /**
     * @var string
     */
    private $imageBaseUrl;

    public function __construct(ImageRepository $imageRepository, string $imageDirectory, string $imageBaseUrl)
    {
        $this->imageRepository = $imageRepository;
        $this->imageDirectory = $imageDirectory;
        $this->imageBaseUrl = $imageBaseUrl;
    }

    /**
     * @Rest\View
     */
    public function getImagesAction()
    {
        return $this->imageRepository->findAll();
    }
    /**
     * @Rest\View(statusCode=201)
     * @Rest\NoRoute()
     * @ParamConverter("image", converter="fos_rest.request_body",
     *      options={"deserializationContext"={"groups"={"Deserialize"}}}
     * )
     */
    public function postImagesAction(Image $image)
    {
        $this->persistImage($image);

        return $this->view($image, Response::HTTP_CREATED)->setHeader(
            'Location',
            $this->generateUrl(
                'images_upload_put',
                ['image' => $image->getId()]
            )
        );
    }

    /**
     * @Rest\NoRoute()
     */
    public function putImageUploadAction(?Image $image, Request $request)
    {
        if (null === $image) {
            throw new NotFoundHttpException();
        }

        // Read the image content from request

        $content = $request->getContent();
        // Create the temporary upload file (deleted after request finishes)
        $tmpFile = tmpfile();

        // Get the temparary file
        $tmpFilePath = \stream_get_meta_data($tmpFile)['uri'];
        file_put_contents($tmpFilePath, $content);

        // Get the file mime-type
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $tmpFilePath);

        // Check if it's really image (don't trust the client set mime type)
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            throw new UnsupportedMediaTypeHttpException(
                "File upload is not a valid jpeg/png/gif image"
            );
        }

        // Guess the extension base on mime-type
        $extensionGuesser = ExtensionGuesser::getInstance();
        //
        $newFileName = md5(uniqid()) . '.' . $extensionGuesser->guess($mimeType);

        // copy the tempfile to the final upload

        copy($tmpFilePath, $this->imageDirectory . DIRECTORY_SEPARATOR . $newFileName);

        $image->setUrl($this->imageBaseUrl . $newFileName);
        $this->persistImage($image);

        return new Response(null, Response::HTTP_OK);

    }

    protected function persistImage(?Image $image): void
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

    }
}
