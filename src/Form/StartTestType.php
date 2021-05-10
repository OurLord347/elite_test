<?php

namespace App\Form;

use App\Entity\Test;
use App\Entity\User;
use App\Entity\TestQuestions;
use App\Entity\Answers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\Request;

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
            ->add('user_id',  HiddenType::class, array(
                'attr' => [
                    'value' => $data['user_id'],
                ]
            ))
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
        //Создаю вопросы
        $this->CreateQuestions($builder,$data);


        //Если нет следующих вопросов то вывожу результат и cоздаю кнопку выход на главную
        if(count($data['questions']) == 0){
            echo $this->CreateAnswers($builder,$data);
            $builder->add('create', ButtonType::class, array(
                'label' => 'Выход',
                'attr' => [
                    'class' => 'btn btn-success float-left mr-3',
                    'onclick' => 'window.location.href = "/"',
                ]
            ));
        }else{
            $builder->add('save', SubmitType::class, array(
                'label' => 'Следующая страница',
                'attr' => [
                    'class' => 'btn btn-success float-left mr-3'
                ]
            ));
        }

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'action' => '/test_start',
        ]);
    }

    private function CreateAnswers($builder,$data){
        //Выбираю нужные записи
        $test_answers =  $data['doctrine']->getRepository(Answers::class);
        $answers = $test_answers->findBy(
            ['test_id' => $data['test_id'],'user_id' => $data['user_id']]
        );

        $test_questions =  $data['doctrine']->getRepository(TestQuestions::class);
        $html = '';
        foreach($answers as $key=>$val){
            $question = $test_questions->find($val->getQuestionId());
            $html .= '<div>';
            $html .= $question->getQuestion().': ';

            switch ($question->getType()){
                case 'one':
                    if($val->getAnswer() == 'yse'){
                        $html .= 'да';
                    }else{
                        $html .= 'нет';
                    }
                    break;
                case  'write':
                        $html .= $val->getAnswer();
                    break;
            }
            $html .= '</div>';
        }
        return $html;
    }
    private function CreateQuestions($builder,$data){
        //Создаю вопросы
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
    //Сохраняю ответы на вопросы
    private function SaveAnswers($data){
        foreach ($data as $key => $val){
            if(strpos($key,'question_') !== false){
                $answer = new Answers();
                $answer->setTestId($data['test_id']);
                $answer->setUserId($data['user_id']);
                $answer->setQuestionId(str_replace('question_','',$key));
                $answer->setAnswer($val);
                $data['em']->persist($answer);
                $data['em']->flush();
            }
        }
        return null;
    }
    private function SetDataLogic($options){
        $data = $options['data'];
        //Передаю в дату данные из прошлой формы
        if(isset($_REQUEST['start_test'])) {
            foreach ($_REQUEST['start_test'] as $key => $val) {
                $data[$key] = $val;
            }
        }

        if(!isset($data['step']) || empty($data['step'])){
            $data['step'] = 0;
        }
        $data['step'] = (int) $data['step'] + 1;
        $data['em'] = $data['doctrine']->getManager();
        //Создаю нового пользователя так как регистрации нет
        if(!isset($data['user_id']) || empty($data['user_id'])){
            $data['user_id'] = $this->SaveNewUser($data);
        }
        //Сохраняю ответы на вопросы если они есть
        $this->SaveAnswers($data);
        //Выбираю нужные записи
        $test_questions =  $data['doctrine']->getRepository(TestQuestions::class);
        $questions = $test_questions->findBy(
            ['test_id' => $data['test_id'],'step' => $data['step']]
        );
        $data['questions'] = $questions;
        return $data;
    }
}
