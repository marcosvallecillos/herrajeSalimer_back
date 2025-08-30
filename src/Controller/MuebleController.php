<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MuebleRepository;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/api/mueble')]

final class MuebleController extends AbstractController
{
    #[Route(name: 'app_mueble_index', methods: ['GET'])]
    public function index(MuebleRepository $muebleRepository): JsonResponse
    {
        $mueble = $muebleRepository->findAll();
        foreach ($muebles as $mueble) {
            $data[] = [
                'id' => $mueble->getId(),
                'nombre' => $mueble->getNombre(),
                'apellidos' => $mueble->getApellidos(),
                'email' => $mueble->getEmail(),
                'telefono' => $mueble->getTelefono(),
                'password' => $mueble->getPassword(),
                'rol' => $mueble->getRol()
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
    
        if (empty($data['password'])) {
            return new JsonResponse(['status' => 'El password es obligatorio'], 400);
        }
    
        $mueble = new Mueble();
        $mueble->setNombre($data['nombre'] ?? null);
        $mueble->setImage($data['imagen'] ?? null);
        $mueble->setNumPieces($data['numero_piezas'] ?? null);
        $entityManager->persist($mueble);
        $entityManager->flush();
    
        return new JsonResponse(['status' => 'mueble creado'], 201);
    }
}
