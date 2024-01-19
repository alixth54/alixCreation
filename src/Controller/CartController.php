<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);
      
        //on creer des variables pour récuperer les informations du panier
        $data = [];
        $total = 0 ;

        //je fais une boucle pour récuperer info panier et les inserer dans variable (si je fais dd($session) je vois panier = id=>quantite)
        foreach($panier as $id => $quantity){
            $product = $productRepository->find($id);

            //je met dans le tableau data les info product
            $data[] = [
                'product'=> $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;      
          }

          return $this->render('cart/index.html.twig', compact('data', 'total'));
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

        //si panier n'existe pas je le creer sinon je l'incremente
        if($panier[$id]= 0){
            $panier[$id]->$session->set('panier', []);
        }else{
            $panier[$id] --;
        }
        
        $session->set('panier', $panier);
        
        
        return $this->redirectToRoute('cart_index');
        
    }
}
