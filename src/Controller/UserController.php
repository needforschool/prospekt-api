<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/create', name: 'app_user_create', methods: ['PUT'])]
    public function create(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent(), true);

        $user =new User();
        $user   ->setEmail($data['email'])
            ->setName($data['name'])
            ->setTel($data['tel'])
            ->setType($data['type'])
            ->setSiret($data['siret'])
            ->setVat($data['vat']);

        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse('success');
    }
}
