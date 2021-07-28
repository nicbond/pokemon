<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Pokemon;
use Symfony\Component\HttpFoundation\Response;

class Validator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        ValidatorInterface $validator
    )
    {
        $this->validator = $validator;
    }

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

    public function fileExist($file)
    {
        if (!file_exists($file)) {
            throw new \Exception('The file mentionned does not exist', Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            return true;
        }
    }

    public function validatorData(Pokemon $pokemon)
    {
        $violations = $this->validator->validate($pokemon);

        if (count($violations)) {
            $message = 'Invalid data. Here are the errors you need to correct: ' .'</br>';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
                $message .= '</br>';
            }
            throw new \Exception($message);
        }
    }
}