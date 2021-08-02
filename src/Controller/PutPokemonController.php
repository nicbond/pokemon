<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PokemonAction;

class PutPokemonController extends AbstractController
{
     /**
     * @var PokemonAction;
     */
    private $pokemonAction;

    public function __construct(PokemonAction $pokemonAction)
    {
        $this->pokemonAction = $pokemonAction;
    }

    public function __invoke(Request $request)
    {
        $id = $request->get('id');
        $data = json_decode($request->getContent(), true);
        $method = 'UPDATE';

        if (empty($data)) {
            throw new \Exception('Sorry, you have not filled in any field', Response::HTTP_BAD_REQUEST);
        }

        $this->pokemonAction->process($id, $method, $data);
    }
}