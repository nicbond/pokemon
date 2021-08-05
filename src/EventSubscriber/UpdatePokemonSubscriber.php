<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Repository\PokemonRepository;
use App\Entity\Pokemon;

class UpdatePokemonSubscriber implements EventSubscriberInterface
{
    /**
     * @var PokemonRepository;
     */
    private $repository;

    public function __construct(PokemonRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ["updatePokemon", EventPriorities::PRE_WRITE]
        ];
    }

    public function updatePokemon(ViewEvent $event)
    {
        //Données du pokemon devant être updaté
        $data = $event->getControllerResult();

        //On récupère la méthode utilisée
        $method = $event->getRequest()->getMethod();

        if (!in_array($method, ['PUT', 'PATCH'])){
            return;
        } else {
            //Anciennes données du pokémon (celles en base)
            $pokemonStored = $event->getRequest()->attributes->get('previous_data');

            if ($pokemonStored->getLegendary() == true) {
                throw new \Exception('Sorry, updating a legendary Pokemon is not allowed', Response::HTTP_BAD_REQUEST);
            } else {
                //Ici on interroge la base de données afin de savoir si le type renseigné est déjà présent
                $type1 = $this->repository->findOneBy(['type1' => $data->getType1()]);

                if (empty($type1)) {
                    throw new \Exception('You cannot add a new type of pokemon: change among the types present please', Response::HTTP_BAD_REQUEST);
                }
            }
        }
    }
}
