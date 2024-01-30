<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Stripe;
use App\Entity\Orders;
use App\Entity\OrderDetail;
use App\Repository\AdressRepository;
use Doctrine\ORM\EntityManagerInterface;
 
class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(ProductRepository $productRepository, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $panier = $session->get('panier', []);

    //on creer des variables pour récuperer les informations du panier
    $totalReduction = 0;
  
    //je fais une boucle pour récuperer info panier et les inserer dans variable (si je fais dd($session) je vois panier = id=>quantite)
    foreach ($panier as $id => $quantity) {
      $product = $productRepository->find($id);

      $totalReduction += ($product->getPrice() - (($product->getPrice() * $product->getDiscount()) / 100)) * $quantity;
    }

        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'totalReduction' => $totalReduction,
        ]);
    }
 
 
    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request,ProductRepository $productRepository, SessionInterface $session,EntityManagerInterface $entity, AdressRepository $adressRepository)
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

try{
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

        $order = new Orders();
    //je vais attribuer a chaque colonne order les infos.
    $order->setUser($this->getUser());
    $adresses = $adressRepository->findBy(['user_id_id' => $this->getUser()]);

    foreach ($adresses as $adress) {

      $order->setAdress($adress);
    }
    
    $order->setPaymentType('CB');
    
    $order->setStatut('reussi');
$order->setTotal($totalReduction);
    $entity->persist($order);
    $entity->flush();
    foreach ($panier as $item => $quantity) {
      $orderDetail = new OrderDetail();
      //je cherche le produit avec repository
      $product = $productRepository->find($item);
      $total = $product->getPrice() - ($product->getPrice() * $product->getDiscount() / 100) * $quantity;
      // dd($product->getName());
      $orderDetail->setProduct($product->getName());
      $orderDetail->setProductDescription($product->getDescription());
      $orderDetail->setProductPrice($product->getPrice());
      $orderDetail->setProductDiscount($product->getDiscount());
      $orderDetail->setQuantity($quantity);

   
      
      $order->getOrderDetails($orderDetail);
      // foreach ($order->getOrderDetails() as $orderDetail) {
        $orderDetail->setOrderId($order);

    //     $entity->persist($orderDetail);
    // }

    $entity->persist($orderDetail);
    $entity->flush();
    }
    


    

        return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    } catch(\Stripe\Exception\CardException $e) {
        // Since it's a decline, Stripe_CardError will be caught
        
        $this->addFlash(
            'warning',
            $e->getMessage()
        );

        $order = new Orders();
        //je vais attribuer a chaque colonne order les infos.
        $order->setUser($this->getUser());
        $adresses = $adressRepository->findBy(['user_id_id' => $this->getUser()]);
    
        foreach ($adresses as $adress) {
    
          $order->setAdress($adress);
        }
        
        $order->setPaymentType('CB');
        
        $order->setStatut($e->getMessage());
    
        $order->setTotal($totalReduction);
        $entity->persist($order);
        $entity->flush();
        foreach ($panier as $item => $quantity) {
          $orderDetail = new OrderDetail();
          //je cherche le produit avec repository
          $product = $productRepository->find($item);
          $total = $product->getPrice() - ($product->getPrice() * $product->getDiscount() / 100) * $quantity;
          // dd($product->getName());
          $orderDetail->setProduct($product->getName());
          $orderDetail->setProductDescription($product->getDescription());
          $orderDetail->setProductPrice($product->getPrice());
          $orderDetail->setProductDiscount($product->getDiscount());
          $orderDetail->setQuantity($quantity);
          $order->getOrderDetails($orderDetail);
          
            $orderDetail->setOrderId($order);
        $entity->persist($orderDetail);
        $entity->flush();
        }

        return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
        
      } catch (\Stripe\Exception\InvalidRequestException $e) {
        // Invalid parameters were supplied to Stripe's API
        $this->addFlash(
            'warning',
            'Contacter l\'administrateur'
        );
      } catch (Stripe\Exception\AuthenticationException $e) {
        // Authentication with Stripe's API failed
        // (maybe you changed API keys recently)
        $this->addFlash(
            'error',
            'Contacter l\'administrateur'
        );
      } catch (Stripe\Exception\AuthenticationException $e) {
        // Network communication with Stripe failed
        $this->addFlash(
            'error',
            'Contacter l\'administrateur'
        );
      // } catch (Exception $e) {
      //   // Something else happened, completely unrelated to Stripe
      //   $this->addFlash(
      //       'error',
      //       'Contacter l\'administrateur'
      //   );
      }


      
  }
}
