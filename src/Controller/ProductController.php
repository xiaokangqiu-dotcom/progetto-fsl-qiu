<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category; // Xiao: Importata l'entità Category per poterla utilizzare
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// Xiao: Controller principale per la gestione delle API dei prodotti
#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Xiao: Decodifica il JSON ricevuto nella richiesta
        $data = json_decode($request->getContent(), true);

        // Xiao: Verifica che i campi obbligatori siano presenti
        if (!isset($data['name']) || !isset($data['price'])) {
            return $this->json(['message' => 'Dati mancanti: nome e prezzo sono obbligatori!'], 400);
        }
        
        // Xiao: Assicura che il prezzo sia un numero valido e maggiore di zero
        if (!is_numeric($data['price']) || $data['price'] <= 0) {
            return $this->json(['message' => 'Il prezzo deve essere un numero maggiore di zero!'], 400);
        }

        // Xiao: Istanzia un nuovo prodotto e assegna i valori
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);

        // Xiao: Se Postman invia un category_id, cerca la categoria e collegala al prodotto
        if (isset($data['category_id'])) {
            $category = $entityManager->getRepository(Category::class)->find($data['category_id']);
            
            if (!$category) {
                return $this->json(['message' => 'Errore: Categoria non trovata!'], 404);
            }
            
            $product->setCategory($category);
        }

        // Xiao: CASCADE PERSIST - Se Postman invia una nuova categoria "nidificata"
        if (isset($data['category']) && isset($data['category']['name'])) {
            // 1. Creiamo l'oggetto Categoria al volo
            $newCategory = new Category();
            $newCategory->setName($data['category']['name']);
            
            // 2. Lo assegniamo al prodotto
            $product->setCategory($newCategory);
        }

        // Xiao: Prepara e salva definitivamente il prodotto nel database
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['message' => 'Prodotto creato!', 'id' => $product->getId()], 201);
    }

    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $offset = ($page - 1) * $limit;

        $repository = $entityManager->getRepository(Product::class);
        $products = $repository->findBy([], null, $limit, $offset);
        
        $totalItems = $repository->count([]);
        $totalPages = ceil($totalItems / $limit);

        $data = [];
        foreach ($products as $p) {
            $data[] = [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'price' => $p->getPrice(),
                'category' => $p->getCategory() ? $p->getCategory()->getName() : null,
            ];
        }

        return $this->json([
            'items' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems
            ]
        ]);
    }

    #[Route('/products/{id}', name: 'product_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        // Xiao: Cerca il prodotto specifico da aggiornare tramite l'ID
        $product = $entityManager->getRepository(Product::class)->find($id);

        // Xiao: Se il prodotto non esiste, blocca tutto e restituisce errore 404
        if (!$product) {
            return $this->json(['message' => 'Prodotto non trovato'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        // Xiao: Aggiorna il nome solo se è stato inviato nel JSON
        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        // Xiao: Aggiorna il prezzo solo se è stato inviato nel JSON
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }

        // Xiao: Aggiorna la categoria se viene inviato un nuovo category_id
        if (isset($data['category_id'])) {
            $category = $entityManager->getRepository(Category::class)->find($data['category_id']);
            
            if (!$category) {
                return $this->json(['message' => 'Errore: Nuova categoria non trovata!'], 404);
            }
            
            $product->setCategory($category);
        }

        // Xiao: Salva le modifiche apportate nel database
        $entityManager->flush();

        return $this->json(['message' => 'Prodotto aggiornato con successo!']);
    }

    #[Route('/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        // Xiao: Trova il prodotto da eliminare
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Prodotto non trovato'], 404);
        }

        // Xiao: Segnala a Doctrine di eliminare il prodotto e applica la modifica
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(['message' => 'Prodotto eliminato!']);
    }
    
    #[Route('/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        // Xiao: Recupera i dettagli di un singolo prodotto usando il suo ID
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Prodotto non trovato'], 404);
        }

        // Xiao: Restituisce direttamente i dati formattati in JSON
        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'category' => $product->getCategory() ? $product->getCategory()->getName() : null,
        ]);
    }
}