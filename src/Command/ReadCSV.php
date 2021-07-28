<?php

namespace App\Command;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use App\Service\Validator;
use App\Service\FileConvert;

class ReadCSV extends Command
{
    /**
     * @param string $filename
    */
    private $filename;

    /**
     * @param string $file
    */
    private $file;

    /**
     * @param datetime $date
    */
    private $date;

    /**
    *   php bin/console import:csv
        Exemple avec date: php bin/console import:csv --env=prod
    */
    protected function configure()
    {
        $this->setName('import:csv')
            ->setDescription('Cron qui lit un fichier CSV');
    }

    public function __construct(ParameterBagInterface $params, Validator $validator, FileConvert $fileConvert, EntityManagerInterface $entityManager, PokemonRepository $repository)
    {
        $this->params = $params;
        $this->validator = $validator;
        $this->fileConvert = $fileConvert;
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_output = $output;
        $this->printLn(''.PHP_EOL);
        $this->printLn('#### Lancement du cron ReadCSV ####'.PHP_EOL);

        $this->date = new \DateTime;
        $this->printLn('Date : '.$this->date->format('Y-m-d'));

        //Récupération du fichier
        $filepath = $this->params->get('upload_dir').'/';
        $file = $filepath.$this->date->format('Y-m-d').'.csv';
        //$file = $filepath.'toto'.'.csv';

        //Vérification de l'existance du fichier CSV
        $this->validator->fileExist($file);

        //Converti le fichier CSV en un tableau 
        $lines = $this->fileConvert->convertToArray($file);

        //Ici j'ai fait comme ci on pouvait recevoir un fichier hebdomadaire afin d'updater les spécificités des pokémons au jour le jour
        if ($lines) {
            foreach ($lines as $line) {
                $name = trim($line[1]);
                $type1 = trim($line[2]);
                $type2 = trim($line[3]);
                $total = trim($line[4]);
                $hp = trim($line[5]);
                $attack = trim($line[6]);
                $defense = trim($line[7]);
                $sp_atk = trim($line[8]);
                $sp_def = trim($line[9]);
                $speed = trim($line[10]);
                $generation = trim($line[11]);
                $legendary = trim($line[12]);

                if ($legendary == 'False') {
                    $legendary = 0;
                } else {
                    $legendary = 1;
                }
                $pokemonSearch = $this->repository->findOneBy(array('name' => $name));

                try {
                    if (is_null($pokemonSearch)) {
                        $pokemon = new Pokemon();
                        $pokemon
                            ->setName($name)
                            ->setType1($type1)
                            ->setType2($type2)
                            ->setTotal($total)
                            ->setHp($hp)
                            ->setAttack($attack)
                            ->setDefense($defense)
                            ->setSpAtk($sp_atk)
                            ->setSpDef($sp_def)
                            ->setSpeed($speed)
                            ->setGeneration($generation)
                            ->setLegendary($legendary);

                        //Contrôle des données avant intégration en base de données
                        $this->validator->validatorData($pokemon);
                        $this->entityManager->persist($pokemon);
                    } else {
                        $pokemonAlreadyExist = $this->repository->find($pokemonSearch->getId());
                        $pokemonAlreadyExist
                            ->setName($name)
                            ->setType1($type1)
                            ->setType2($type2)
                            ->setTotal($total)
                            ->setHp($hp)
                            ->setAttack($attack)
                            ->setDefense($defense)
                            ->setSpAtk($sp_atk)
                            ->setSpDef($sp_def)
                            ->setSpeed($speed)
                            ->setGeneration($generation)
                            ->setLegendary($legendary);

                        //Contrôle des données avant intégration en base de données
                        $this->validator->validatorData($pokemonAlreadyExist);
                    }
                    $this->entityManager->flush();
                } catch (\Doctrine\ORM\ORMException $e) {
                    $errorMsg = 'Error Doctrine for the pokemon '.$name.'<br/>'.$e->getMessage();
                }
            }
        }
        $this->printLn('#### Fin du cron ReadCSV ####');
        return 0;
    }

    protected function printLn($line): void
    {
        if ($this->_output->isVerbose()) {
            $this->_output->writeln($line);
        }
    }
}