<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\InvoiceRepository;
use App\Repository\InvoiceItemRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class InvoiceItemController extends AbstractController
{
    #[Route('/invoice/item', name: 'app_invoice_item')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InvoiceItemController.php',
        ]);
    }

    // get invoiceItem by idInvoice

    #[Route('/invoiceitem/{id}', name: 'app_invoiceitem_id', methods: ['GET'])]
    public function itemByInvoiceId(InvoiceRepository $invoiceRepository, $id): Response
    {
        $invoice = $invoiceRepository->find($id);
        if (!$invoice) {
            throw $this->createNotFoundException(
                'No invoice found with id '.$id
            );
        }

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

    #[Route('/invoiceitems', name: 'app_invoiceitems', methods: ['GET'])]
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

    #[Route('/add/invoiceitem', name: 'app_api_add_invoiceitem', methods: ['POST'])]
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
