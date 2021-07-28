<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserControl;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
     /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    #[Route('/api/registration', name: 'registration', methods: ['GET','POST'])]
    public function new(Request $request, UserControl $control): Response
    {
        //Récupére les données du body et les convertit dans un tableau associatif
        $data = json_decode($request->getContent(), true);

        //Vérification si l'utilisateur a bien renseigné les champs requis
        $controlFields = $control->fieldsRequire($data);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        //Vérification des données: si elles sont conformes
        $controlData = $control->validatorData($user);

        //Hachage du mot de passe grâce à l'encodeur
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('Registration completed: user created successfully', Response::HTTP_CREATED);
    }
}
