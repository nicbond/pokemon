<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Controller\DeletePokemonController;
use App\Controller\PutPokemonController;

/**
 * @ORM\Entity(repositoryClass=PokemonRepository::class)
 */
#[ApiResource(
    collectionOperations: ['get'],
    security: 'is_granted("ROLE_USER")',
    normalizationContext: ['groups' => ['read:collection']],
    denormalizationContext: ['groups' => ['write:Pokemon']],
    paginationItemsPerPage: 50,
    paginationMaximumItemsPerPage: 50,
    paginationClientItemsPerPage: true,
    itemOperations: [
        'put' => [
            'path' => '/pokemon/{id}',
            'method' => 'PUT',
            'controller' => PutPokemonController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ]
        ],
        'delete' => [
            'path' => '/pokemon/{id}',
            'method' => 'DELETE',
            'controller' => DeletePokemonController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ]
        ],
        'get' => [
            'normalization_context' => ['groups' => ['read:collection', 'read:item']]
        ]
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial', 'type1' => 'exact', 'generation' => 'exact'])]
#[ApiFilter(BooleanFilter::class, properties: ['legendary' => 'exact'])]
class Pokemon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection', 'read:item'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection', 'read:item', 'write:Pokemon'])]
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection', 'read:item', 'write:Pokemon'])]
    private $type1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['read:collection', 'read:item'])]
    private $type2;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item'])]
    private $total;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item'])]
    private $hp;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item'])]
    private $attack;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item'])]
    private $defense;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item'])]
    private $sp_atk;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item'])]
    private $sp_def;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item'])]
    private $speed;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    #[Groups(['read:item', 'write:Pokemon'])]
    private $legendary;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item', 'write:Pokemon'])]
    private $generation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType1(): ?string
    {
        return $this->type1;
    }

    public function setType1(string $type1): self
    {
        $this->type1 = $type1;

        return $this;
    }

    public function getType2(): ?string
    {
        return $this->type2;
    }

    public function setType2(?string $type2): self
    {
        $this->type2 = $type2;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getHp(): ?int
    {
        return $this->hp;
    }

    public function setHp(int $hp): self
    {
        $this->hp = $hp;

        return $this;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getSpAtk(): ?int
    {
        return $this->sp_atk;
    }

    public function setSpAtk(int $sp_atk): self
    {
        $this->sp_atk = $sp_atk;

        return $this;
    }

    public function getSpDef(): ?int
    {
        return $this->sp_def;
    }

    public function setSpDef(int $sp_def): self
    {
        $this->sp_def = $sp_def;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getLegendary(): ?bool
    {
        return $this->legendary;
    }

    public function setLegendary(?bool $legendary): self
    {
        $this->legendary = $legendary;

        return $this;
    }

    public function getGeneration(): ?int
    {
        return $this->generation;
    }

    public function setGeneration(int $generation): self
    {
        $this->generation = $generation;

        return $this;
    }
}
