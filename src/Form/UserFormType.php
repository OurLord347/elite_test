<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Ваше имя',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('test_id', EntityType::class, [
                'class' => Test::class,
                'label' => 'Название теста',
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Начать',
                'attr' => [
                    'class' => 'btn btn-success float-left mr-3'
                ]
            ))->add('create', ButtonType::class, array(
                'label' => 'Создать тест',
                'attr' => [
                    'class' => 'btn btn-success float-left mr-3',
                    'onclick' => 'window.location.href = "/create_test"',
                ]
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'action' => '/test_start'
        ]);
    }
}
