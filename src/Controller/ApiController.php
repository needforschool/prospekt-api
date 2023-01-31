<?php

namespace App\Controller;

use App\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Repository\InvoiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/users', name: 'app_api_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {

        $users = [];
        foreach ($userRepository->findAll() as $user) {
            $users[] = $user->getInfos();
        }
        return $this->json($users, Response::HTTP_OK);

    }


    
}
