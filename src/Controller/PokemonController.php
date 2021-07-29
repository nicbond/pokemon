<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class PokemonController extends AbstractController
{
     /**
     * @var PokemonRepository;
     */
    private $repository;
     /**
     * @var SerializerInterface;
     */
    private $serializer;

    public function __construct(PokemonRepository $repository, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    #[Route('/api/pokemon', name: 'api_pokemon_get_collection')]
    public function index()
    {
        $pokemons = $this->repository->findAll();
        return $pokemons;
    }

    #[Route('/api/pokemon/{id}', name: 'api_pokemon_get_item')]
    public function show($id)
    {
        $pokemon = $this->repository->find($id);

        if (empty($pokemon)){
            throw new \Exception('The resource does not exist', Response::HTTP_BAD_REQUEST);
        }
        return $pokemon;
    }
}