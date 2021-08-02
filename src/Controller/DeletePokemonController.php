<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PokemonAction;

class DeletePokemonController extends AbstractController
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
        $data = [];
        $method = 'DELETE';

        $this->pokemonAction->process($id, $method, $data);
    }
}