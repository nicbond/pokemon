<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use App\Service\Validator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadFileController extends AbstractController
{
    /**
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param Validator $validator
     * @param SerializerInterface $serializer
     * @return Response
     * @throws \Exception
     */
    #[Route('/upload', name: 'upload')]
    public function index(Request $request, string $uploadDir, FileUploader $uploader, Validator $validator, SerializerInterface $serializer): Response
    {
        //Date du jour
        $date = new \DateTime();
        $today = $date->format('Y-m-d');

        /** @var UploadedFile $uploadedFile */
        $file = $request->files->get('attachment');

        //Vérification du format du fichier autorisé et retour des erreurs éventuelles
        $key = $validator->validatorFile($file);

        //Nouveau nom du fichier avec la date du jour
        $extensionFile = $key['extensionFile'];
        $newFilename = $today . '.' . $extensionFile;

        //Appel du service FileUploader
        $uploader->upload($uploadDir, $file, $newFilename);

        //Si pas d'erreur via le validator, affichage du message d'upload
        $data[] = ['message' => 'CSV uploaded'];
        return new Response($serializer->serialize($data, 'json', []));
    }
}