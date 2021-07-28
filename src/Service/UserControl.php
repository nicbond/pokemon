<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class UserControl
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

    public function fieldsRequire(array $data)
    {
        if (!array_key_exists('email', $data)) {
            throw new \Exception('The email field is required', Response::HTTP_BAD_REQUEST);
        }
        if (!array_key_exists('password', $data)) {
            throw new \Exception('The password field is required', Response::HTTP_BAD_REQUEST);
        }
    }

    public function validatorData(User $user)
    {
        $violations = $this->validator->validate($user);

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