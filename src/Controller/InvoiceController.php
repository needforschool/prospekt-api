<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Repository\InvoiceRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController

{
    protected $entityManager;

    public function __construct(ManagerRegistry $doctrine){
        $this->entityManager = $doctrine->getManager();
    }

    // get all invoices

    #[Route('/invoices', name: 'app_invoices', methods: ['GET'])]
    public function invoices(InvoiceRepository $invoiceRepository): Response
    {
        $invoices = $invoiceRepository->findAll();
        $data = [];
        for($i = 0; $i < count($invoices); $i++) {
            $data[] = [
                'id'          => $invoices[$i]->getId(),
                'customer_id' => $invoices[$i]->getCustomerId()->getId(),
                'uuid'        => $invoices[$i]->getUuid(),
                'status'      => $invoices[$i]->getStatus(),
                'created_at'  => $invoices[$i]->getCreatedAt(),
                'due_at'      => $invoices[$i]->getDueAt(),
                'issued_at'   => $invoices[$i]->getIssuedAt()
            ];
        }

        return $this->json($data, Response::HTTP_OK);

    }

    #[Route('/invoice', name: 'app_invoice')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InvoiceController.php',
        ]);
    }

    //create invoice

    #[Route('/invoice/Create', name: 'app_invoice_create', methods: ['POST'])]
    public function createInvoice(Request $request,ManagerRegistry $doctrine): Response {

        $data = json_decode($request->getContent(), true);

        $user = $doctrine->getRepository(User::class)->find($data['idCustomer']);
        $invoice = new invoice();
        $invoice->setCustomerId($user)
                ->setStatus($data['name'])
                ->setCreatedAt(new \DateTimeImmutable());

        try{
            $this->entityManager->persist($invoice);
            $this->entityManager->flush();
        }catch(\Exception $e){
            return $this->json($e->getMessage(), 500);
        }

        return $this->json(['message' => 'success'], Response::HTTP_OK);
    }

    #[Route('/invoiceDelete/{id}', name: 'app_invoice_delete', methods: ['DELETE'])]
    public function deleteInvoice(Request $request,ManagerRegistry $doctrine, Invoice $id): Response {

        try{
            $this->entityManager->remove($id);
            $this->entityManager->flush();
        }catch(\Exception $e){
            return  $this->json($e->getMessage(),500);
        }

        //catch
        return $this->json(['message' => 'success'], Response::HTTP_NO_CONTENT);

    }

    #[Route('invoiceUpdate/{id}', name: 'app_invoice_update', methods: ['PATCH'])]
    public function updateInvoice(Request $request,ManagerRegistry $doctrine, int $id): Response {


        $data = json_decode($request->getContent(), true);

        $invoice = $doctrine->getRepository(Invoice::class)->find($id);// redeclarer le $invoice

        if (isset($data['status'])) {
            $invoice->setStatus($data['status']);
        }

        if (isset($data['dueAt'])) {
            $invoice->setDueAt(DateTimeImmutable::createFromFormat('Y-m-d',$data['dueAt']));
        }

        if (isset($data['issueAt'])) {
            $invoice->setIssuedAt(DateTimeImmutable::createFromFormat('Y-m-d',$data['issueAt']));
        }

        try{
            $this->entityManager->flush();
        }catch(\Exception $e){
            return $this->json($e->getMessage(),500);
        }

        return $this->json(['message' => 'success'], Response::HTTP_OK);
    }

    // get invoice selon son id

    #[Route('/invoice/{id}', name: 'app_api_invoice_id', methods: ['GET'])]
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


    // to do ajout de getInvoiceByCustomerId
}
