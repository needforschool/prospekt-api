<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\User;
use App\Repository\InvoiceItemRepository;
use App\Repository\InvoiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
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
