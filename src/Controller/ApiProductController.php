<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiProductController extends AbstractController
{
    #[Route('/api/product', name: 'api_product')]
    public function getProduct(ProductRepository $productRepository, NormalizerInterface $normalizerInterface){

        $product = $productRepository->findAll();
        $serializeProduct = $normalizerInterface->normalize($product , 'json',[
            'groups' => 'product:item'
        ]);
        return $this->json($serializeProduct);

    }

}