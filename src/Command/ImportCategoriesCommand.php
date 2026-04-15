<?php

namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// Xiao: Qui diamo il nome al comando che digiteremo nel terminale
#[AsCommand(
    name: 'app:import-categories',
    description: 'Importa un elenco di categorie da un file CSV locale',
)]
class ImportCategoriesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    // Xiao: Iniettiamo l'EntityManager per poter salvare i dati nel database
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Xiao: Diciamo a Symfony dove si trova il nostro file CSV
        $csvPath = __DIR__.'/../../data/categorie.csv';

        // Xiao: Controllo di sicurezza, se il file non esiste blocchiamo tutto
        if (!file_exists($csvPath)) {
            $io->error('Il file CSV non è stato trovato in: ' . $csvPath);
            return Command::FAILURE;
        }

        // Xiao: Apriamo il file in modalità lettura ('r')
        $file = fopen($csvPath, 'r');
        $isFirstRow = true;

        // Xiao: Leggiamo il file riga per riga. fgetcsv trasforma ogni riga in un array
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            
            // Xiao: Se è la primissima riga (l'intestazione "name"), la saltiamo e passiamo alla successiva
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // Xiao: Creiamo una nuova Categoria per ogni riga del CSV
            $category = new Category();
            // La colonna 0 del nostro CSV contiene il nome della categoria
            $category->setName($row[0]); 

            // Xiao: Prepariamo il salvataggio
            $this->entityManager->persist($category);
        }

        // Xiao: Chiudiamo il file per liberare memoria
        fclose($file);
        
        // Xiao: Eseguiamo un'unica grande query per salvare tutto nel database in un colpo solo
        $this->entityManager->flush();

        // Xiao: Mostriamo un messaggio di successo nel terminale
        $io->success('Fatto! Le categorie sono state importate dal file CSV.');

        return Command::SUCCESS;
    }
}