<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\AdressRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Orders;
use App\Entity\OrderDetail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Stripe\Stripe;


#[Route('/order', name: 'order_')]
class OrderController extends AbstractController
{
    #[Route('/ckeckout', name: 'checkout')]
    public function orderAdd(Request $request, SessionInterface $session, EntityManagerInterface $entity, ProductRepository $productRepository, AdressRepository $adressRepository): Response
    {
        //pour valider si user creer
        $this->denyAccessUnlessGranted('ROLE_USER');
      //je recupere variable session avec panier dedans
        $panier = $session->get('panier',[]);
  //je creer un nouvel order pour le mettre en bdd
        $order = new Orders();
        //je vais attribuer a chaque colonne order les infos.
        $order->setUser($this->getUser());
        $adresses = $adressRepository->findBy(['user_id_id'=>$this->getUser()]);
       
         foreach($adresses as $adress){
       
        $order->setAdress($adress);

        }
        if ($request->get('payment_type')) {
            dd($request->get('payment_type'));
            $order->setPaymentType($request->get('payment_type'));
           
        }
        $order->setStatut('non-aboutit');

        
foreach($panier as $item=>$quantity){
    $orderDetail = new OrderDetail();
    //je cherche le produit avec repository
    $product = $productRepository->find($item);
    $total = $product->getPrice()-($product->getPrice()*$product->getDiscount()/100)*$quantity;
    // dd($product->getName());
    $orderDetail->setProduct($product->getName());
    $orderDetail->setProductDescription($product->getDescription());
    $orderDetail->setProductPrice($product->getPrice());
    $orderDetail->setProductDiscount($product->getDiscount());
    $orderDetail->setQuantity($quantity);
    
    $order->setQuantity(0);
    $order->setTotal($total);
    $order->getOrderDetails($orderDetail);

    
}
            $entity->persist($order);
            $entity->flush();

            // foreach ($order->getOrderDetails() as $orderDetail) {
                 $orderDetail->setOrderId($order);
                 
            //     $entity->persist($orderDetail);
            // }
          
            $entity->persist($orderDetail);
            $entity->flush();

        if($panier === []){
            $this->addFlash('message','Votre panier est vide');
            return $this->redirectToRoute('home');
        }


        return $this->render('order/index.html.twig', [
            
            
        ]);
    }

    #[Route('/payment', name: 'payment')]
    public function index(Request $request, ProductRepository $productRepository, SessionInterface $session): Response
    {

      $panier = $session->get('panier', []);
      
        //on creer des variables pour récuperer les informations du panier
        $data = [];
        $total = 0 ;
        $totalReduction = 0;
        $discounttotal = 0;

        //je fais une boucle pour récuperer info panier et les inserer dans variable (si je fais dd($session) je vois panier = id=>quantite)
        foreach($panier as $id => $quantity){
            $product = $productRepository->find($id);

            //je met dans le tableau data les info product
            $data[] = [
                'product'=> $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
            $discounttotal += (($product->getPrice()*$product->getDiscount())/100)* $quantity;
            $totalReduction += ($product->getPrice() - (($product->getPrice()*$product->getDiscount())/100)) * $quantity;
          }
		$form = $this->createFormBuilder(null)
            ->add('save', SubmitType::class, ['label' => 'Submit Payment'])
            ->getForm();
		
		if ($request->get('stripeToken')) {
			$stripe = new Stripe();
			$stripe->setVerifySslCerts(false);
			//à remplacer par la secret key live quand on passe en production
			$stripe->setApiKey("sk_test_51ObOMoK60wgWalzXZXApOWlX8ozDPlCpgTecqPivR7H5U1kt2mHW369jiFWc9L8GAUvGSxwz1JlbLQpSg3ntezRS00DfEcacsO");
			try {
					 
				// This is a 1.00 charge in EUR.
				$charge = \Stripe\Charge::create(
					array(
						'amount' => $totalReduction * 100,
						'currency' => 'eur',
						'source' => $request->get('stripeToken')
					)
				);
				if ($charge->paid==true) {
					return $this->render('payment/success.html.twig',[]);
				}
				
			} catch(Stripe_CardError $e) {
			  // Since it's a decline, Stripe_CardError will be caught
			  dd($e);
			  
			} catch (Stripe_InvalidRequestError $e) {
			  // Invalid parameters were supplied to Stripe's API
			} catch (Stripe_AuthenticationError $e) {
			  // Authentication with Stripe's API failed
			  // (maybe you changed API keys recently)
			} catch (Stripe_ApiConnectionError $e) {
			  // Network communication with Stripe failed
			} catch (Stripe_Error $e) {
			  // Display a very generic error to the user, and maybe send
			  // yourself an email
			} catch (Exception $e) {
			  // Something else happened, completely unrelated to Stripe
			}
			
		}
		
        return $this->render('order/index.html.twig', [
            'form' => $form,
        ]);
    }
}
