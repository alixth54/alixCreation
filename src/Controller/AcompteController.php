<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Adress;
use App\Entity\User;
use App\Form\AdressType;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/accompte', name: 'accompte_')]
class AcompteController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $adresses = $entityManager->getRepository(Adress::class)->findBy(['user_id_id'=>$this->getUser()]);
        
        return $this->render('acompte/index.html.twig', [
            'adresses' => $adresses,
        ]);
    }

    #[Route('/addAdress', name: 'addAdress')]
    public function addAdress(Request $request, EntityManagerInterface $entity): Response
    {
        
        $adress = new Adress();
        $form = $this->createForm(AdressType::class, $adress);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $adress->setUserId($this->getUser());
            
            $entity->persist($adress);
            $entity->flush();

            return $this->redirectToRoute('accompte_index',[$this->addFlash(
                'success',
                'ajout de la tâche réussi'
            )]);
        }
        return $this->render('acompte/formAdress.html.twig', [
            'form'=>$form->createView(),
            
        ]);
    }

    #[Route('/editAdress/{id}', name: 'editAdress')]
    public function editAdress(Request $request, EntityManagerInterface $entity, int $id): Response
    {
        $adress = $entity->getRepository(Adress::class)->find($id);
        $form = $this->createForm(AdressType::class, $adress);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entity->persist($adress);
            $entity->flush();
            return $this->redirectToRoute('accompte_index',[$this->addFlash(
                'success',
                'Modification de l\'adresse réussie'
            )]);
        }

        return $this->render('acompte/formAdress.html.twig', [
            'form'=>$form->createView(),
            
        ]);
    }

    #[Route('/deleteAdress/{id}', name: 'deleteAdress')]
    public function deleteAdress(EntityManagerInterface $entityManager, int $id): Response
    {
        $adress = $entityManager->getRepository(Adress::class)->find(['id'=>$id]);
        
        $entityManager->remove($adress);
        $entityManager->flush();
    

return $this->redirectToRoute('accompte_index',[
$this->addFlash(
    'success',
    'suppression de l\'adresse réussie')
]);
    }

    #[Route('/editUser/{id}', name: 'editUser')]
    public function editUser(Request $request, EntityManagerInterface $entity, int $id, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $entity->getRepository(User::class)->find($id);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );


            
            $entity->persist($user);
            $entity->flush();
            return $this->redirectToRoute('accompte_index',[$this->addFlash(
                'success',
                'Modification réussie'
            )]);
        }

        return $this->render('acompte/formUser.html.twig', [
            'registrationForm'=>$form->createView(),
            
        ]);
    }
}
