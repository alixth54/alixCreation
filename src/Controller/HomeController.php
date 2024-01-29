<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Entity\Product;

class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(EntityManagerInterface $entityManager): Response
    {  
        $categorys = $entityManager->getRepository(Category::class)->findAll();
        $products = $entityManager->getRepository(Product::class)->findAll();
        
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categorys' => $categorys,
        ]);
    }
}
