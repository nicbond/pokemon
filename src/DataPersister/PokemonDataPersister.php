<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PokemonRepository;
use App\Entity\Pokemon;

class PokemonDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private PokemonRepository $repository, private EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Pokemon;
    }

    public function persist($data, array $context = [])
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        if ($data->getLegendary() == false) {
            $this->entityManager->remove($data);
            $this->entityManager->flush();
        } else {
            throw new \Exception('Sorry, deleting a legendary Pokemon is not allowed', Response::HTTP_BAD_REQUEST); 
        }
    }
}
