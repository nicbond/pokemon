<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PokemonRepository;
use App\Entity\Pokemon;

class PokemonAction
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
     /**
     * @var PokemonRepository;
     */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, PokemonRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    public function process(int $id, string $method)
    {
        $pokemon = $this->repository->find($id);

        if ($pokemon->getLegendary() == false) {
            if ($method == 'DELETE') {
                $this->delete($pokemon);
            }
            if ($method == 'UPDATE') {
                $this->update($pokemon);
            }
        } else {
            throw new \Exception('Sorry, updating or deleting a legendary Pokemon is not allowed', Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Pokemon $pokemon)
    {
        var_dump('je peux updater mon pokemon');die;
    }

    public function delete(Pokemon $pokemon)
    {
        $this->entityManager->remove($pokemon);
        $this->entityManager->flush();
    }
}