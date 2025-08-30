<?php

namespace App\Controller;

use App\Entity\Herrajes;
use App\Repository\HerrajesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/herrajes')]

final class HerrajesController extends AbstractController
{
    #[Route(name: 'app_herrajes_index', methods: ['GET'])]
    public function index(HerrajesRepository $herrajesRepository): JsonResponse
    {
         $herrajes = $herrajesRepository->findAll();
        $data = [];
        foreach ($herrajes as $herraje) {
            $data[] = [
                'id' => $herraje->getId(),
                'cantidad' => $herraje->getCantidad(),
                'tipo' => $herraje->getTipo(),
                'mueble_id' => $herraje->getMuebleId(),
            ];
        }
        
        return new JsonResponse($data);
    }
     #[Route('/new', name: 'app_herrajes_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $data = json_decode($request->getContent(), true);

    // Verificar si el JSON está bien formado
    if ($data === null) {
        return new JsonResponse(['status' => 'JSON inválido', 'error' => json_last_error_msg()], 400);
    }

    // Verificar que los campos requeridos estén presentes
    if (!isset($data['nombre'], $data['imagen'], $data['numero_piezas'])) {
        return new JsonResponse(['status' => 'Faltan campos requeridos', 'error' => 'nombre, imagen, numero_piezas'], 400);
    }

    // Crear un nuevo objeto Herrajes
    $herraje = new Herrajes();
    $herraje->setCantidad($data['cantidad']);
    $herraje->setTipo($data['tipo']);
    $herraje->setMuebleId($data['mueble_id']);

    // Persistir el herraje
    $entityManager->persist($herraje);
    $entityManager->flush();

    return new JsonResponse(['status' => 'Herraje creado', 'id' => $herraje->getId()], 201);
}

 #[Route('/{id}', name: 'app_herrajes_show', methods: ['GET'])]
    public function show(Herrajes $herraje): JsonResponse
    {
        $data = [
            'id' => $herraje->getId(),
            'cantidad' => $herraje->getCantidad(),
            'tipo' => $herraje->getTipo(),
            'mueble_id' => $herraje->getMuebleId(),
        ];
        
        return new JsonResponse($data);
    }

    #[Route('/{id}/edit', methods: ['GET', 'PUT'], name: 'app_herrajes_edit')]
    public function edit(Request $request, Herrajes $herraje, EntityManagerInterface $entityManager): JsonResponse
    {
        // If it's a GET request, return the herraje data
        if ($request->getMethod() === 'GET') {
                $data = [
                     'id' => $herraje->getId(),
            'cantidad' => $herraje->getCantidad(),
            'tipo' => $herraje->getTipo(),
            'mueble_id' => $herraje->getMuebleId(),
                ];
                
                return new JsonResponse($data);
            }
            
            // For PUT requests, update the user
            $data = json_decode($request->getContent(), true); // Se recibe la información en JSON.

            // Actualizamos los campos del herraje con los datos recibidos
            $herraje->setCantidad($data['cantidad'] ?? $herraje->getCantidad());
            $herraje->setTipo($data['tipo'] ?? $herraje->getTipo());
            $herraje->setMuebleId($data['mueble_id'] ?? $herraje->getMuebleId());

            $entityManager->flush();

            return new JsonResponse(['status' => 'Herraje actualizado']);
        }

   #[Route('/delete/{id}', name: 'app_herrajes_delete', methods: ['DELETE'])]
    public function delete(Herrajes $herraje, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($herraje);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Herraje eliminado con éxito'], 200);
    }
    }

