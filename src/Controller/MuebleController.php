<?php

namespace App\Controller;
use App\Entity\Herrajes;

use App\Entity\Mueble;
use App\Repository\MuebleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;



#[Route('/api/mueble', name: 'app_mueble')]
final class MuebleController extends AbstractController
{
    #[Route(name: 'app_mueble_index', methods: ['GET'])]
   public function index(MuebleRepository $muebleRepository): JsonResponse
{
    $muebles = $muebleRepository->findAll();
    $data = [];
    foreach ($muebles as $mueble) {
        $herrajesData = [];
        foreach ($mueble->getHerrajes() as $herraje) {
            $herrajesData[] = [
                'id' => $herraje->getId(),
                'tipo' => $herraje->getTipo(),
                'cantidad' => $herraje->getCantidad(),
            ];
        }

        $data[] = [
            'id' => $mueble->getId(),
            'nombre' => $mueble->getNombre(),
            'imagen' => $mueble->getImage(),
            'numero_piezas' => $mueble->getNumPieces(),
            'herrajes' => $herrajesData,  // Aquí agregamos los herrajes serializados
        ];
    }
      return new JsonResponse($data);
}

    #[Route('/search', name: 'app_mueble_search', methods: ['GET'])]
    public function search(Request $request, MuebleRepository $muebleRepository): JsonResponse
    {
        $nombre = $request->query->get('nombre');
        
        if (!$nombre) {
            return new JsonResponse(['error' => 'El parámetro "nombre" es requerido'], 400);
        }
        
        $muebles = $muebleRepository->findByNombre($nombre);
        $data = [];
        
        foreach ($muebles as $mueble) {
             $herrajesData = [];
        foreach ($mueble->getHerrajes() as $herraje) {
            $herrajesData[] = [
                'id' => $herraje->getId(),
                'tipo' => $herraje->getTipo(),
                'cantidad' => $herraje->getCantidad(),
            ];
        }

            $data[] = [
                'id' => $mueble->getId(),
                'nombre' => $mueble->getNombre(),
                'imagen' => $mueble->getImage(),
                'numero_piezas' => $mueble->getNumPieces(),
                'herrajes' => $herrajesData,
            ];
        }
        
        return new JsonResponse($data);
    }
    
  
    


        #[Route('/new', name: 'app_mueble_search', methods: ['GET', 'POST'])]

  public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $data = json_decode($request->getContent(), true);

    if ($data === null) {
        return new JsonResponse(['status' => 'JSON inválido', 'error' => json_last_error_msg()], 400);
    }

    if (!isset($data['nombre'], $data['imagen'], $data['numero_piezas'])) {
        return new JsonResponse(['status' => 'Faltan campos requeridos', 'error' => 'nombre, imagen, numero_piezas'], 400);
    }

    $mueble = new Mueble();
    $mueble->setNombre($data['nombre']);
    $mueble->setImage($data['imagen']);
    $mueble->setNumPieces($data['numero_piezas']);

    // Crear herrajes si vienen
    if (!empty($data['herrajes']) && is_array($data['herrajes'])) {
        foreach ($data['herrajes'] as $herrajes) {
            if (!isset($herrajes['tipo'], $herrajes['cantidad'])) {
                continue; // Ignorar herrajes inválidos
            }
            $herraje = new Herrajes();
            $herraje->setTipo($herrajes['tipo']);
            $herraje->setCantidad($herrajes['cantidad']);
            $herraje->setMuebleId($mueble); // Relación inversa
            $entityManager->persist($herraje);
            $mueble->addHerraje($herraje);
        }
    }

    try {
        $entityManager->persist($mueble);
        $entityManager->flush();
    } catch (\Exception $e) {
        return new JsonResponse([
            'status' => 'Error al guardar',
            'message' => $e->getMessage()
        ], 500);
    }

    return new JsonResponse(['status' => 'Mueble creado', 'id' => $mueble->getId()], 201);
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
            
            // For PUT requests, update the entity
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                return new JsonResponse(['status' => 'JSON inválido', 'error' => json_last_error_msg()], 400);
            }

            // Actualizamos campos simples
            if (array_key_exists('nombre', $data)) {
                $mueble->setNombre($data['nombre']);
            }
            if (array_key_exists('imagen', $data)) {
                $mueble->setImage($data['imagen']);
            }
            if (array_key_exists('numero_piezas', $data)) {
                $mueble->setNumPieces((int) $data['numero_piezas']);
            }

            // Sincronizar herrajes si vienen en el payload
            if (array_key_exists('herrajes', $data) && is_array($data['herrajes'])) {
                $existingHerrajes = $mueble->getHerrajes();

                $existingById = [];
                foreach ($existingHerrajes as $existing) {
                    if (null !== $existing->getId()) {
                        $existingById[$existing->getId()] = $existing;
                    }
                }

                $payloadIdsToKeep = [];

                foreach ($data['herrajes'] as $hData) {
                    // Validar estructura mínima
                    if (!is_array($hData)) {
                        continue;
                    }

                    $id = $hData['id'] ?? null;
                    $tipo = $hData['tipo'] ?? null;
                    $cantidad = $hData['cantidad'] ?? null;

                    if ($id !== null && isset($existingById[$id])) {
                        // Actualizar existente
                        $herraje = $existingById[$id];
                        if ($tipo !== null) {
                            $herraje->setTipo($tipo);
                        }
                        if ($cantidad !== null) {
                            $herraje->setCantidad((int) $cantidad);
                        }
                        $payloadIdsToKeep[] = $id;
                    } else {
                        // Crear nuevo
                        if ($tipo === null || $cantidad === null) {
                            // si faltan datos básicos, ignorar
                            continue;
                        }
                        $nuevo = new Herrajes();
                        $nuevo->setTipo($tipo);
                        $nuevo->setCantidad((int) $cantidad);
                        $nuevo->setMuebleId($mueble);
                        $entityManager->persist($nuevo);
                        $mueble->addHerraje($nuevo);
                        // no tiene id aún hasta flush
                    }
                }

                // Eliminar los herrajes que ya no vienen en el payload
                foreach ($existingHerrajes as $existing) {
                    $existingId = $existing->getId();
                    if ($existingId !== null && !in_array($existingId, $payloadIdsToKeep, true)) {
                        $mueble->removeHerraje($existing);
                        $entityManager->remove($existing);
                    }
                }
            }

            $entityManager->flush();

            // Responder con el mueble actualizado serializado
            $herrajesData = [];
            foreach ($mueble->getHerrajes() as $herraje) {
                $herrajesData[] = [
                    'id' => $herraje->getId(),
                    'tipo' => $herraje->getTipo(),
                    'cantidad' => $herraje->getCantidad(),
                ];
            }

            $dataResponse = [
                'id' => $mueble->getId(),
                'nombre' => $mueble->getNombre(),
                'imagen' => $mueble->getImage(),
                'numero_piezas' => $mueble->getNumPieces(),
                'herrajes' => $herrajesData,
            ];

            return new JsonResponse($dataResponse, 200);
        }

  #[Route('/delete/{id}', name: 'app_mueble_delete', methods: ['DELETE'])]
public function delete(Mueble $mueble, EntityManagerInterface $entityManager): JsonResponse
{
    // Eliminar herrajes asociados
    foreach ($mueble->getHerrajes() as $herraje) {
        $entityManager->remove($herraje);
    }

    $entityManager->remove($mueble);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Mueble eliminado con éxito'], 200);
}

}
