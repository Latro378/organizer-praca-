<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class)
            ->add('email',EmailType::class)
            ->add('imie',TextType::class )
            ->add('nazwisko',TextType::class )
            ->add('password',RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Hasło '],
                'second_options' => ['label' => 'Powtórz Hasło']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success pull-right'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \AppBundle\Entity\User::class
        ]);
    }

}