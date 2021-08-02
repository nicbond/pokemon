<?php

namespace App\Service;

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

    public function process(int $id, string $method, array $data)
    {
        $pokemon = $this->repository->find($id);

        if ($pokemon->getLegendary() == false) {
            if ($method == 'DELETE') {
                $this->delete($pokemon);
            }
            if ($method == 'UPDATE') {
                $this->update($pokemon, $data);
            }
        } else {
            throw new \Exception('Sorry, updating or deleting a legendary Pokemon is not allowed', Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Pokemon $pokemon, array $data)
    {
        $update = false;

        if (array_key_exists('name', $data)) {
            $pokemon->setName($data['name']);
            $update = true;
        }
        if (array_key_exists('generation', $data)) {
            $pokemon->setGeneration($data['generation']);
            $update = true;
        }
        if (array_key_exists('legendary', $data)) {
            $pokemon->setLegendary($data['legendary']);
            $update = true;
        }
        if (array_key_exists('type1', $data)) {
            $pokemon->setType1($data['type1']);
            $type1 = $this->repository->findOneBy(['type1' => $data['type1']]);

            if (empty($type1)) {
                throw new \Exception('You cannot add a new type of pokemon: change among the types present please', Response::HTTP_BAD_REQUEST);
            } else {
                $update = true;
            }
        }

        if ($update == true) {
            $this->entityManager->persist($pokemon);
            $this->entityManager->flush();
        } else {
            throw new \Exception('Only the name, type, legendary, generation fields are authorized', Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Pokemon $pokemon)
    {
        $this->entityManager->remove($pokemon);
        $this->entityManager->flush();
    }
}