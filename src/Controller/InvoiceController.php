<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'app_invoice')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InvoiceController.php',
        ]);
    }
    #[Route('/invoiceCreate', name: 'app_invoice', methods: ['POST'])]
    public function createInvoice(Request $request,ManagerRegistry $doctrine): Response {
        $entityManager = $doctrine->getManager();

        $invoice = new invoice();

        $data = json_decode($request->getContent(), true);

        $user = $doctrine->getRepository(User::class)->find($data['idCustomer']);

        $invoice->setCustomerId($user)
                ->setStatus($data['name'])
                ->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($invoice);
        $entityManager->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);

    }

    #[Route('/invoiceDelete/{id}', name: 'app_invoice', methods: ['DELETE'])]
    public function deleteInvoice(Request $request,ManagerRegistry $doctrine, int $id): Response {

        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent(), true);

        $user = $doctrine->getRepository(User::class)->find($data['idCustomer']);

        $invoice = $doctrine->getRepository(Invoice::class)->find($id);

        $entityManager->remove($invoice);
        $entityManager->flush();
        return $this->json(['status' => 'success'], Response::HTTP_OK);

    }

    #[Route('/invoiceUpdate/{id}', name: 'app_invoice', methods: ['DELETE'])]
    public function updateInvoice(Request $request,ManagerRegistry $doctrine, int $id): Response {

        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent(), true);

        $user = $doctrine->getRepository(User::class)->find($data['idCustomer']);

        $invoice = $doctrine->getRepository(Invoice::class)->find($id);




        $entityManager->remove($invoice);
        $entityManager->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);

    }

}
