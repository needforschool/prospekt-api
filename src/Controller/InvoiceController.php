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
    #[Route('/invoice', name: 'app_invoice')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InvoiceController.php',
        ]);
    }
    #[Route('/invoiceCreate', name: 'app_invoice_create', methods: ['POST'])]
    public function createInvoice(Request $request,ManagerRegistry $doctrine): Response {

        $data = json_decode($request->getContent(), true);
        // erreur si customer id n'existe pas on boucle sur name et id pour verifier que NN renvoi une bad request
        $user = $doctrine->getRepository(User::class)->find($data['idCustomer']);
        $invoice = new invoice();
        $invoice->setCustomerId($user)
                ->setStatus($data['name'])
                ->setCreatedAt(new \DateTimeImmutable());
        // try
        $this->entityManager->persist($invoice);
        $this->entityManager->flush();
        // catch renvoi erreur 500 "une erreur lors de l'enregistrement c'est produite.."
        // monolog log le message d'erreur
        return $this->json(['message' => 'success'], Response::HTTP_OK);
    }

    #[Route('/invoiceDelete/{id}', name: 'app_invoice_delete', methods: ['DELETE'])]
    public function deleteInvoice(Request $request,ManagerRegistry $doctrine, Invoice $id): Response {

        // try
        $this->entityManager->remove($id);
        $this->entityManager->flush();
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
        // try
        $this->entityManager->flush();
        // catch
        return $this->json(['message' => 'success'], Response::HTTP_OK);
    }

    #[Route('/invoices/{id}', name: 'app_api_invoices', methods: ['GET'])]
    public function getMyInvoices(InvoiceRepository $invoiceRepository, UserRepository $userRepo, ManagerRegistry $doctrine, $id): Response
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
