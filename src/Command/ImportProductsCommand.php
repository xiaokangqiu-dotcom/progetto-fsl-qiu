<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import-products',
    description: 'Importa prodotti da CSV e gestisce le categorie in automatico',
)]
class ImportProductsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $params;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Xiao: Troviamo il percorso del file in modo sicuro come l'altra volta
        $projectDir = $this->params->get('kernel.project_dir');
        $csvPath = $projectDir . '/data/prodotti.csv';

        if (!file_exists($csvPath)) {
            $io->error('File CSV non trovato in: ' . $csvPath);
            return Command::FAILURE;
        }

        $file = fopen($csvPath, 'r');
        $isFirstRow = true;

        // Xiao: Creiamo un "cestino della memoria" temporaneo.
        // Ci serve per ricordarci quali categorie abbiamo già visto mentre leggiamo il CSV,
        // per evitare di creare "Periferiche PC" due volte se ci sono due mouse di fila.
        $categoryCache = [];

        // Xiao: Ci prepariamo a cercare le categorie nel database
        $categoryRepo = $this->entityManager->getRepository(Category::class);

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // Xiao: Estraiamo i dati dalla riga del CSV
            $productName = trim($row[0]);
            $productPrice = (float) trim($row[1]); // Trasformiamo il prezzo in numero
            $categoryName = trim($row[2]);

            // --- GESTIONE INTELLIGENTE DELLA CATEGORIA ---
            
            // 1. Controlliamo se ce la siamo già salvata nella memoria temporanea di questo script
            if (isset($categoryCache[$categoryName])) {
                $category = $categoryCache[$categoryName];
            } else {
                // 2. Se non c'è in memoria, la cerchiamo nel database vero e proprio
                $category = $categoryRepo->findOneBy(['name' => $categoryName]);

                // 3. Se non esiste nemmeno nel database, la creiamo nuova!
                if (!$category) {
                    $category = new Category();
                    $category->setName($categoryName);
                    // Non serve $entityManager->persist($category) grazie al tuo cascade: ['persist']!
                }
                
                // Salviamo la categoria nella memoria temporanea per i prossimi giri del ciclo
                $categoryCache[$categoryName] = $category;
            }

            // --- CREAZIONE DEL PRODOTTO ---
            $product = new Product();
            $product->setName($productName);
            $product->setPrice($productPrice);
            $product->setCategory($category); // Assegniamo la categoria (vecchia o nuova che sia)

            $this->entityManager->persist($product);
        }

        fclose($file);
        
        // Xiao: Salviamo tutti i prodotti e le eventuali nuove categorie in un colpo solo
        $this->entityManager->flush();

        $io->success('Importazione completata! I prodotti (e le nuove categorie) sono nel database.');

        return Command::SUCCESS;
    }
}