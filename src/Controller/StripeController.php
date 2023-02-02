<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use Stripe\Stripe;
use Stripe\Charge;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    // get le montant d'une invoice

    #[Route('/amountinvoice/{id}', name: 'app_amountinvoice', methods: ['GET'])]
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

    //tarif interface

    #[Route('/amountinvoice/{id}/stripe', name: 'app_stripe')]
    public function index(InvoiceRepository $invoiceRepository, $id): Response
    {
        $id = $invoiceRepository->find($id);
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'id' => $id
        ]);

    }


    #[Route('/amountinvoice/{id}/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, InvoiceController $invoiceController, InvoiceRepository $invoiceRepository ,$id)
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Charge::create ([
            "amount" => (int)($this->sumAmountItemByInvoiceId($invoiceRepository, $id)->getContent()* 100),  // en centimes de base
            "currency" => "eur",
            "source" => $request->request->get('stripeToken'),
            "description" => "Paiement de facture"
        ]);
        $this->addFlash(
            'success',
            'Paiement effectué !'
        );
        return $this->redirectToRoute('app_stripe', ['id' => $id], Response::HTTP_SEE_OTHER);
    }





}