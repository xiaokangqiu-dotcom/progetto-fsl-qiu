<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['message' => 'Prodotto creato!', 'id' => $product->getId()], 201);
    }

    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        $data = [];

        foreach ($products as $p) {
            $data[] = [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'price' => $p->getPrice(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/products/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Prodotto non trovato'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Prodotto aggiornato con successo!']);
    }

    #[Route('/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Prodotto non trovato'], 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(['message' => 'Prodotto eliminato!']);
    }
}