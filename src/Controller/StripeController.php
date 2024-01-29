<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Stripe;
 
class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }
 
 
    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request,ProductRepository $productRepository, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

    //on creer des variables pour récuperer les informations du panier
    $data = [];
    $total = 0;
    $totalReduction = 0;
    $discounttotal = 0;

    //je fais une boucle pour récuperer info panier et les inserer dans variable (si je fais dd($session) je vois panier = id=>quantite)
    foreach ($panier as $id => $quantity) {
      $product = $productRepository->find($id);

      //je met dans le tableau data les info product
      $data[] = [
        'product' => $product,
        'quantity' => $quantity
      ];
      $total += $product->getPrice() * $quantity;
      $discounttotal += (($product->getPrice() * $product->getDiscount()) / 100) * $quantity;
      $totalReduction += ($product->getPrice() - (($product->getPrice() * $product->getDiscount()) / 100)) * $quantity;
    }


        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create ([
                "amount" => $totalReduction * 100,
                "currency" => "eur",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    }
}