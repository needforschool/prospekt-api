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


    #[Route('/api/amountinvoice/{id}/stripe', name: 'app_stripe')]
    public function index(InvoiceRepository $invoiceRepository, $id): Response
    {   
        $id = $invoiceRepository->find($id);
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'id' => $id
        ]);
        
    }

    
    #[Route('/api/amountinvoice/{id}/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, ApiController $apiController, InvoiceRepository $invoiceRepository ,$id)
    { 
        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Charge::create ([
                "amount" => (int)($apiController->sumAmountItemByInvoiceId($invoiceRepository, $id)->getContent()* 100),  // en centimes de base
                "currency" => "eur",
                "source" => $request->request->get('stripeToken'),
                "description" => "Paiement de facture"
        ]);
        $this->addFlash(
            'success',
            'Paiement effectué !'
        );
        return $this->redirect('/thanks', Response::HTTP_SEE_OTHER);
        //return $this->redirectToRoute('/thanks', ['id' => $id], Response::HTTP_SEE_OTHER);
        //return new Response('Paiement effectué !', Response::HTTP_OK);
    }

    // #[Route('/stripe', name: 'app_stripe')]
    // public function index(): Response
    // {
    //     return $this->render('stripe/index.html.twig', [
    //         'stripe_key' => $_ENV["STRIPE_KEY"],
    //     ]);
    // }

    // #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    // public function createCharge(Request $request)
    // {
    //     Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
    //     Charge::create ([
    //             "amount" => 5 * 100,
    //             "currency" => "eur",
    //             "source" => $request->request->get('stripeToken'),
    //             "description" => "Test de paiement"
    //     ]);
    //     $this->addFlash(
    //         'success',
    //         'Payment Successful!'
    //     );
    //     return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    // }
}
