<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// Xiao: Controller principale per la gestione delle API delle categorie
#[Route('/api', name: 'api_')]
class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Xiao: Decodifica il JSON ricevuto nella richiesta
        $data = json_decode($request->getContent(), true);

        // Xiao: Verifica che i campi obbligatori siano presenti (per la categoria serve solo il nome)
        if (!isset($data['name'])) {
            return $this->json(['message' => 'Dati mancanti: il nome della categoria è obbligatorio!'], 400);
        }

        // Xiao: Istanzia una nuova categoria e assegna il nome
        $category = new Category();
        $category->setName($data['name']);

        // Xiao: Prepara e salva definitivamente la categoria nel database
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json(['message' => 'Categoria creata con successo!', 'id' => $category->getId()], 201);
    }

    #[Route('/categories', name: 'category_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Xiao: Leggiamo la pagina attuale (default 1) e quanti elementi per pagina (default 5)
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 5);
        $offset = ($page - 1) * $limit;

        $repository = $entityManager->getRepository(Category::class);
        
        // Xiao: Recuperiamo le categorie paginate
        $categories = $repository->findBy([], null, $limit, $offset);
        
        // Xiao: Contiamo quante categorie totali esistono nel database
        $totalItems = $repository->count([]);
        $totalPages = ceil($totalItems / $limit);

        $data = [];
        foreach ($categories as $c) {
            $data[] = [
                'id' => $c->getId(),
                'name' => $c->getName(),
            ];
        }

        // Xiao: Restituiamo i dati E le informazioni della paginazione (Meta-dati)
        return $this->json([
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'items_per_page' => $limit,
                'total_items' => $totalItems,
                'total_pages' => $totalPages
            ]
        ]);
    }

    #[Route('/categories/{id}', name: 'category_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        // Xiao: Cerca la categoria specifica da aggiornare tramite l'ID
        $category = $entityManager->getRepository(Category::class)->find($id);

        // Xiao: Se la categoria non esiste, blocca tutto e restituisce errore 404
        if (!$category) {
            return $this->json(['message' => 'Categoria non trovata'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        // Xiao: Aggiorna il nome solo se è stato inviato nel JSON
        if (isset($data['name'])) {
            $category->setName($data['name']);
        }

        // Xiao: Salva le modifiche apportate nel database
        $entityManager->flush();

        return $this->json(['message' => 'Categoria aggiornata con successo!']);
    }

    #[Route('/categories/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        // Xiao: Trova la categoria da eliminare
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json(['message' => 'Categoria non trovata'], 404);
        }

        // Xiao: Segnala a Doctrine di eliminare la categoria e applica la modifica
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->json(['message' => 'Categoria eliminata!']);
    }
    
    #[Route('/categories/{id}', name: 'category_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        // Xiao: Recupera i dettagli di una singola categoria usando il suo ID
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json(['message' => 'Categoria non trovata'], 404);
        }

        // Xiao: Restituisce direttamente i dati formattati in JSON
        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
        ]);
    }
}