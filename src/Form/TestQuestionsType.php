<?php

namespace App\Form;

use App\Entity\TestQuestions;
use App\Entity\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TestQuestionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question',  TextareaType::class, array(
                'label' => 'Вопрос',
                'attr' => [
                    'placeholder' => 'Введите вопрос'
                ]
            ))
            ->add('type', ChoiceType::class, array(
                'label' => 'Тип вопроса',
                'mapped' => false,
                'choices' => array(
                    'Один ответ'=> 'one',
//                    'Несколько ответов'=> 'many',
                    'Записать овтет' => 'write',
                )
            ))
            ->add('test_id', EntityType::class, [
                // looks for choices from this entity
                'class' => Test::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
//                 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('create_new_test',  CheckboxType::class, array(
                'label' => 'Создать новый Тест',
            ))
            ->add('test_name',  TextType::class, array(
                'label' => 'Название теста',
                'attr' => [
                    'placeholder' => 'Введите название'
                ]
            ))
            ->add('steep', ChoiceType::class, array(
                'label' => 'Ступень',
                'mapped' => false,
                'choices' => array(
                    'Первая '=> '1',
                    'Вторая'=> '2',
                    'Третья' => '3',
                )
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Сохранить',
                'attr' => [
                    'class' => 'btn btn-success float-left mr-3'
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TestQuestions::class,
            'action' => '/save_question'
        ]);
    }
}
