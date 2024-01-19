<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Choice;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address_info', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('adress_info2', TextType::class, [
                'label' => 'Complément d\'adresse',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('zipcode', NumberType::class, [
                'label' => 'Code Postale',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Adresse de',
                'choices'  => [
                    'Facturation' => 'Facturation',
                    'Livraison' => 'Livraison',
                    
                ],
            ])
            // ->add('user_id', EntityType::class, [
            //     'class' => User::class,
                

            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
