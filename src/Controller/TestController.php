<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\TestQuestionsType;
use App\Form\UserFormType;
use App\Form\StartTestType;
use App\Entity\TestQuestions;
use App\Entity\Test;
use App\Entity\User;

class TestController extends AbstractController
{
    /**
     * @Route("/", name="start")
     */
    public function index(): Response
    {
        $form = $this->createForm(UserFormType::class);
        return $this->render('user_register.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/test_start", name="test_start")
     */
    public function test_start(Request $request): Response
    {
        $UserData = $request->get('user_form');
        //Да криво но по другому я не понял как мне вызвать в форме менеджер
        $UserData['doctrine'] = $this->getDoctrine();

        $form = $this->createForm(StartTestType::class,$UserData);
        return $this->render('test_start.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/create_test", name="create_test")
     */
    public function create_test(): Response
    {
        $form = $this->createForm(TestQuestionsType::class);
        return $this->render('test_questions.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/save_question", name="save_question")
     */
    public function save_question(Request $request): Response
    {
        try{
            $question = new TestQuestions();
            //Если не заполнены основые данные то выдает ошибку
            $questionReq = $request->get('test_questions');
            if (empty($questionReq['question'])){
                throw new \Exception();
            }
            $questionReq['test_id'] = $this->SaveTest($questionReq);

            $question->setQuestion($questionReq['question'])
                ->setTestId($questionReq['test_id'])
                ->setType($questionReq['type'])
                ->setSteep($questionReq['steep']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Articles added successfully",
            ];
            return $this->response($data);
        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }
    }
    private function SaveTest($questionReq){
        if($questionReq['create_new_test'] && !empty($questionReq['test_name'])){
            $test = new Test();
            $test->setName($questionReq['test_name']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($test);
            $entityManager->flush();
            return $test->getId();
        }
        return null;
    }
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }
}
