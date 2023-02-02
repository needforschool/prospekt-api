<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\UserLog;
use App\Repository\InvoiceItemRepository;
use App\Repository\InvoiceRepository;
use App\Repository\UserLogRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
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

    #[Route('/api/user/{id}', name: 'app_api_user_id', methods: ['GET'])]
    public function userById(UserRepository $userRepository, $id): Response
    {
       

        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(
                'No user found with id '.$id
            );
        }
        $user = $userRepository->find($id)->getInfos();
        return $this->json($user, Response::HTTP_OK);

    }

    #[Route('/api/add/user', name: 'app_api_add_user', methods: ['POST'])]
    public function addUser(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $user = new User();
        
        $user->setType($data['type']);
        $user->setEmail($data['email']);
        $user->setTel($data['tel']);
        $user->setName($data['name']);
        $user->setPassword($data['password']);
        $user->setToken($data['token']);
        $user->setSiret($data['siret']);
        $user->setVat($data['vat']);
        $user->setUpdatedAt	($data['updated_at']);
        $user->setCreatedAt($data['created_at']);

        $entityManager->persist($user);
        $entityManager->flush();
        //return new JsonResponse('success');
        return $this->json($user, Response::HTTP_OK);

    }

    
    #[Route('/api/invoices', name: 'app_api_invoices', methods: ['GET'])]
    public function invoices(InvoiceRepository $invoiceRepository): Response
    {
        $invoices = $invoiceRepository->findAll();
        $data = [];
        for($i = 0; $i < count($invoices); $i++){
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

    #[Route('/api/invoice/{id}', name: 'app_api_invoice_id', methods: ['GET'])]
    public function invoiceById(InvoiceRepository $invoiceRepository, $id): Response
    {
        if (!$invoiceRepository->find($id)) {
            throw $this->createNotFoundException(
                'No invoice found with id '.$id
            );
        }
  
        $invoice = $invoiceRepository->find($id);    
        $data = [];

        $data[] = [
            'id' => $invoice->getId(),
            'customer_id' => $invoice->getCustomerId()->getId(),
            'uuid' => $invoice->getUuid(),
            'status' => $invoice->getStatus(),
            'created_at' => $invoice->getCreatedAt(),
            'due_at' => $invoice->getDueAt(),
            'issued_at' => $invoice->getIssuedAt()
        ];
        
        return $this->json($data, Response::HTTP_OK);   
    }

    #[Route('/api/add/invoice', name: 'app_api_add_invoice', methods: ['POST'])]
    public function addInvoice(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);  
        $invoice = new Invoice();
        $user = $entityManager->getRepository(User::class)->find($data['customer_id']);
        $invoice->setCustomerId($user);
        
        $invoice->setCustomerId($user);
        $invoice->setUuid($data['uuid']);
        $invoice->setStatus($data['status']);
        $invoice->setCreatedAt($data['created_at']);
        $invoice->setDueAt($data['due_at']);
        $invoice->setIssuedAt($data['issued_at']);
        
        $entityManager->persist($user);
        $entityManager->persist($invoice);
        $entityManager->flush();
        //return new JsonResponse('success');
        return $this->json($invoice, Response::HTTP_OK);

    }


    #[Route('/api/userlogs', name: 'app_api_userlog', methods: ['GET'])]
    public function userLogs(UserLogRepository $userLogRepository): Response
    {
        $userLogs = $userLogRepository->findAll();
        $data = [];

        for($i = 0; $i < count($userLogs); $i++){
            $data[] = [
                'id' => $userLogs[$i]->getId(),
                'author_id' => $userLogs[$i]->getAuthorId()->getId(),
                'target_id' => $userLogs[$i]->getTargetId()->getId(),
                'type' => $userLogs[$i]->getType(),
                'content' => $userLogs[$i]->getContent(),
                'created_at' => $userLogs[$i]->getCreatedAt()
            ];
        }
        
        return $this->json($data, Response::HTTP_OK);   
    }

    #[Route('/api/add/userlog', name: 'app_api_add_userlog', methods: ['POST'])]
    public function addUserLog(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);  
        $userLog = new UserLog();

        $userAuthor = $entityManager->getRepository(User::class)->find($data['author_id']);
        $userTarget = $entityManager->getRepository(User::class)->find($data['target_id']);
        
        $userLog->setAuthorId($userAuthor);
        $userLog->setTargetId($userTarget);
        
        $userLog->setType($data['type']);
        $userLog->setContent($data['content']);
        $userLog->setCreatedAt($data['created_at']);
        
        $entityManager->persist($userAuthor);
        $entityManager->persist($userTarget);
        $entityManager->persist($userLog);
        $entityManager->flush();
        //return new JsonResponse('success');
        return $this->json($userLog, Response::HTTP_OK);
    }

    
    #[Route('/api/invoiceitems', name: 'app_api_invoiceitems', methods: ['GET'])]
    public function invoiceItems(InvoiceItemRepository $invoiceItemRepository): Response
    {
        $invoiceItems = $invoiceItemRepository->findAll();
        $data = [];

        for($i = 0; $i < count($invoiceItems); $i++){
            $data[] = [
                'id' => $invoiceItems[$i]->getId(),
                'invoice_id' => $invoiceItems[$i]->getInvoiceId()->getId(),
                'name' => $invoiceItems[$i]->getName(),
                'description' => $invoiceItems[$i]->getDescription(),
                'amount' => $invoiceItems[$i]->getAmount(),
                'unit_price' => $invoiceItems[$i]->getUnitPrice()
            ];
        }
        
        return $this->json($data, Response::HTTP_OK);   
    }

    #[Route('/api/invoiceitem/{id}', name: 'app_api_invoiceitem_id', methods: ['GET'])]
    public function itemByInvoiceId(InvoiceRepository $invoiceRepository, $id): Response
    {
        if (!$invoiceRepository->find($id)) {
            throw $this->createNotFoundException(
                'No invoice found with id '.$id
            );
        }
        $invoice = $invoiceRepository->find($id);
        $items = $invoice->getInvoiceItems(); // instead of find

        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'invoice_id' => $item->getInvoiceId()->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'amount' => $item->getAmount(),
                'unit_price' => $item->getUnitPrice()
            ];
        }
        
        return $this->json($data, Response::HTTP_OK);   

    }

    #[Route('/api/amountinvoice/{id}', name: 'app_api_amountinvoice', methods: ['GET'])]
    public function sumAmountItemByInvoiceId(InvoiceRepository $invoiceRepository, $id): Response
    {
        if (!$invoiceRepository->find($id)) {
            throw $this->createNotFoundException(
                'No invoice found with id '.$id
            );
        }
        $invoice = $invoiceRepository->find($id);
        $items = $invoice->getInvoiceItems();
        $amount = 0;
        foreach ($items as $item) { 
            
            $amount = $amount + $item->getAmount();
        }
        
        return $this->json($amount, Response::HTTP_OK);   
    }



    #[Route('/api/add/invoiceitem', name: 'app_api_add_invoiceitem', methods: ['POST'])]
    public function addInvoiceItem(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);  
        $invoiceItem = new InvoiceItem();

        $invoice = $entityManager->getRepository(Invoice::class)->find($data['invoice_id']);
        
        $invoiceItem->setInvoiceId($invoice); 
        $invoiceItem->setName($data['name']);
        $invoiceItem->setDescription($data['description']);
        $invoiceItem->setAmount($data['amount']);
        $invoiceItem->setUnitPrice($data['unit_price']);
        
        $entityManager->persist($invoice);
        $entityManager->persist($invoiceItem);
        $entityManager->flush();
        //return new JsonResponse('success');
        return $this->json($invoiceItem, Response::HTTP_OK);
    }

    

    
}
