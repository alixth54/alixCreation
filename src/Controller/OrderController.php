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
    $panier = $session->get('panier', []);
    //je creer un nouvel order pour le mettre en bdd
    $order = new Orders();
    //je vais attribuer a chaque colonne order les infos.
    $order->setUser($this->getUser());
    $adresses = $adressRepository->findBy(['user_id_id' => $this->getUser()]);

    foreach ($adresses as $adress) {

      $order->setAdress($adress);
    }
    if ($request->get('payment_type')) {
      dd($request->get('payment_type'));
      $order->setPaymentType($request->get('payment_type'));
    }
    $order->setStatut('non-aboutit');


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

    if ($panier === []) {
      $this->addFlash('message', 'Votre panier est vide');
      return $this->redirectToRoute('home');
    }


    return $this->render('order/index.html.twig', []);
  }

}
