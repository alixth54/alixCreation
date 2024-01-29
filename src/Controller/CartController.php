<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, EntityManagerInterface $entity, ProductRepository $productRepository)
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
          
          return $this->render('cart/index.html.twig', 
          compact('data', 'total','totalReduction','discounttotal'), 
          
            
        );
    }

    #[Route('/add/{id}', name: 'add')]
    public function add(product $product, SessionInterface $session)
    {
        //je récupère id produit
        $id = $product->getId();

        // je recupere le panier
        $panier = $session->get('panier', []);

        //si panier n'existe pas je le creer sinon je l'incremente
        if(empty($panier[$id])){
            $panier[$id] = 1;
        }else{
            $panier[$id] ++;
        }
        
        $session->set('panier', $panier);
        
        
        return $this->redirectToRoute('cart_index');
        
    }

    #[Route('/minus/{id}', name: 'minus')]
    public function minus(product $product, SessionInterface $session)
    {
        //je récupère id produit
        $id = $product->getId();

        // je recupere le panier
        $panier = $session->get('panier', []);

        //si panier est vide je retire le rpoduit sinon je décremente
        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id] --;
            }else{
                unset($panier[$id]);
                
            }
        }
        
        $session->set('panier', $panier);
        
        
        return $this->redirectToRoute('cart_index');
        
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(product $product, SessionInterface $session)
    {
        //je récupère id produit
        $id = $product->getId();

        // je recupere le panier
        $panier = $session->get('panier', []);

        //si panier n'eest pas vide je l'efface.
        if(!empty($panier[$id])){
            unset($panier[$id]); 
            }
        
        
        $session->set('panier', $panier);
        
        
        return $this->redirectToRoute('cart_index');
        
    }
}
