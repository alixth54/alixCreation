<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SousCategory;
use App\Entity\Product;

class FilterController extends AbstractController
{
    #[Route('/filter/{id}', name: 'filter')]
    public function index(EntityManagerInterface $entityManager, int $id): Response
    {
        $categorys = $entityManager->getRepository(SousCategory::class)->findby(['id_souscategory'=>$id]);
        $products = $entityManager->getRepository(Product::class)->findBy(['category_id'=>$id]);
        
        return $this->render('filter/index.html.twig', [
            'products' => $products,
            'categorys' =>$categorys,

        ]);
    }

    #[Route('/sousfilter/{id}', name: 'sousfilter')]
    public function souscatefiltre(EntityManagerInterface $entityManager, int $id): Response
    {
        $categorys = $entityManager->getRepository(SousCategory::class)->findby(['id_souscategory'=>$id]);
        $products = $entityManager->getRepository(Product::class)->findBy(['souscate'=>$id]);
        
        return $this->render('filter/sousfiltre.html.twig', [
            'products' => $products,
            'categorys' =>$categorys,

        ]);
    }
} 
