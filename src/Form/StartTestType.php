<?php

namespace App\Form;

use App\Entity\Test;
use App\Entity\User;
use App\Entity\TestQuestions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;

class StartTestType extends AbstractType
{
    protected $em;

    public function __constructor(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    public function buildForm(FormBuilderInterface $builder, $options)
    {
        $data = $this->SetDataLogic($options);
        $builder
//            ->add('user_id',  HiddenType::class, array(
//                'attr' => [
//                    'value' => $data['user_id'],
//                ]
//            ))
            ->add('step',  HiddenType::class, array(
                'attr' => [
                    'value' => $data['step'],
                ]
            ))
            ->add('test_id',  HiddenType::class, array(
                'attr' => [
                    'value' => $data['test_id'],
                ]
            ));
        foreach ($data['questions'] as $key => $val){
            switch ($val->getType()){
                case 'one':
                    $builder->add('question_'.$val->getId(),  ChoiceType::class, array(
                        'label' => $val->getQuestion(),
                        'mapped' => false,
                        'choices' => array(
                            'Да'=> 'yse',
                            'Нет'=> 'no',
                        )
                    ));
                    break;
                case  'write':
                    $builder->add('question_'.$val->getId(),  TextareaType::class, array(
                        'label' => $val->getQuestion(),
                        'attr' => [
                            'placeholder' => 'Введите ответ'
                        ]
                    ));
                    break;
            }
        }
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
    private function SaveNewUser($data){
        if(!empty($data['name'])
            && !empty(!empty($data['test_id']))
        ){
            $user = new User();
            $user->setName($data['name']);
            $user->setTestId($data['test_id']);

            $data['em']->persist($user);
            $data['em']->flush();

            return $user->getId();
        }
        return null;
    }
    private function SetDataLogic($options){
        $data = $options['data'];
        if(!isset($data['step']) || empty($data['step'])){
            $data['step'] = 0;
        }
        $data['step'] = (int) $data['step'] + 1;
        $data['em'] = $data['doctrine']->getManager();
//        if(!isset($data['user_id']) || empty($data['user_id'])){
//            $data['user_id'] = $this->SaveNewUser($data);
//        }
        //Выбираю нужные записи
        $test_questions =  $data['doctrine']->getRepository(TestQuestions::class);
        $questions = $test_questions->findBy(
            ['test_id' => $data['test_id'],'step' => $data['step']]
        );
        $data['questions'] = $questions;
        return $data;
    }
}
