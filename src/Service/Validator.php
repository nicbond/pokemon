<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class Validator
{
    public function validatorFile($file): array
    {
        $data = array();
        $fileExtension = '';

        //S'il n'y a pas de fichier uploadé
        if (empty($file)) {
            throw new \Exception('File upload is required', Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $fileExtension = $file->guessExtension();

            //Si le fichier uploadé est différent du format csv
            if ($fileExtension != 'csv') {
                throw new \Exception('CSV file are only allowed', Response::HTTP_BAD_REQUEST);
            } else {
                $data['extensionFile'] = $file->guessExtension();
            }
        }
        return $data;
    }
}