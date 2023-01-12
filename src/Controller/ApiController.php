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

    #[Route('/api/invoices', name: 'app_api_invoices', methods: ['GET'])]
    public function invoices(InvoiceRepository $invoiceRepository, UserRepository $userRepo): Response
    {
  
        $invoices = $invoiceRepository->findAll();
        $data = [];
        for($i = 0; $i< count($invoices); $i++){
            $data[] = [
                'id' => $invoices[$i]->getId(),
                'customer_id' => $invoices[$i]->getCustomerId()->getId(),
                'uuid' => $invoices[$i]->getUuid(),
                'status' => $invoices[$i]->getStatus(),
                'created_at' => $invoices[$i]->getCreatedAt(),
                'due_at' => $invoices[$i]->getDueAt(),
                'issued_at' => $invoices[$i]->getIssuedAt()
            ];
        }

        return $this->json($data, Response::HTTP_OK);      
    
    }

    
}
