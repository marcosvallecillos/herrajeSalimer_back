<?php

namespace App\Controller;

use App\Entity\Mueble;
use App\Repository\MuebleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/mueble', name: 'app_mueble')]
final class MuebleController extends AbstractController
{
    #[Route(name: 'app_mueble_index', methods: ['GET'])]
    public function index(MuebleRepository $muebleRepository): JsonResponse
    {
        $muebles = $muebleRepository->findAll();
        $data = [];
        foreach ($muebles as $mueble) {
            $data[] = [
                'id' => $mueble->getId(),
                'nombre' => $mueble->getNombre(),
                'Imagen' => $mueble->getImage(),
                'NumeroPiezas' => $mueble->getNumPieces(),
                'Herrajes' => $mueble->getHerrajes(),
            ];
        }
        
        return new JsonResponse($data);
    }
    


    
    #[Route('/new', name: 'app_mueble_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
    
        if ($data === null) {
            return new JsonResponse(['status' => 'JSON inválido'], 400);
        }
    
        $mueble = new Mueble();
        $mueble->setNombre($data['nombre'] ?? null);
        $mueble->setImage($data['imagen'] ?? null);
        $mueble->setNumPieces($data['numero_piezas'] ?? null);
        $entityManager->persist($mueble);
        $entityManager->flush();
    
        return new JsonResponse(['status' => 'mueble creado'], 201);
    }

      #[Route('/{id}', name: 'app_mueble_show', methods: ['GET'])]
    public function show(Mueble $mueble): JsonResponse
    {
        $data = [
            'id' => $mueble->getId(),
            'nombre' => $mueble->getNombre(),
            'imagen' => $mueble->getImage(),
            'numero_piezas' => $mueble->getNumPieces(),
            'herrajes' => $mueble->getHerrajes(),
        ];
        
        return new JsonResponse($data);
    }

    #[Route('/{id}/edit', methods: ['GET', 'PUT'], name: 'app_mueble_edit')]
    public function edit(Request $request, Mueble $mueble, EntityManagerInterface $entityManager): JsonResponse
    {
        // If it's a GET request, return the mueble data
        if ($request->getMethod() === 'GET') {
                $data = [
                    'id' => $mueble->getId(),
                    'nombre' => $mueble->getNombre(),
                    'imagen' => $mueble->getImage(),
                    'numero_piezas' => $mueble->getNumPieces(),
                    'herrajes' => $mueble->getHerrajes(),
                ];
                
                return new JsonResponse($data);
            }
            
            // For PUT requests, update the user
            $data = json_decode($request->getContent(), true); // Se recibe la información en JSON.

            // Actualizamos los campos del mueble con los datos recibidos
            $mueble->setNombre($data['nombre'] ?? $mueble->getNombre());
            $mueble->setImage($data['imagen'] ?? $mueble->getImage());
            $mueble->setNumPieces($data['numero_piezas'] ?? $mueble->getNumPieces());
            $mueble->setHerrajes($data['herrajes'] ?? $mueble->getHerrajes());


            $entityManager->flush();

            return new JsonResponse(['status' => 'Mueble actualizado']);
        }

    #[Route('/delete/{id}', name: 'app_usuarios_delete', methods: ['DELETE'])]
    public function delete(Usuarios $usuario, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($usuario);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Usuario eliminado con éxito'], 200);
    }
}
