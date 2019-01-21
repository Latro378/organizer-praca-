<?php

namespace AppBundle\Controller;


use AppBundle\AppBundle;
use AppBundle\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends Controller
{
    /**
     * @Route("/todo", name="todo_list")
     */
    public function listAction()
    {
        $user = $this->getUser();
        $todos = $this->getDoctrine()->getManager()
            ->getRepository(Todo::class)->findBy(['user' => $user]);

        return $this->render('Todo/todo.html.twig', array(
            'todos' => $todos
        ));
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo();

        $formTodo = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('label' => 'Nazwa', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('label' => 'Kategoria', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('label' => 'Priorytet', 'choices' => array('Niska' => 'Niska', 'Średnia' => 'Średnia', 'Wysoka' => 'Wysoka'), 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('label' => 'Opis', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('date', DateTimeType::class, array('label' => 'Data rozpoczęcia', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('dateEnd', DateTimeType::class, array('label' => 'Data zakonczenia', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('day', IntegerType::class, array('label' => 'Co ile dni(Jeżeli wydarzenie jest jednorazowe - wpisz 0)', 'mapped' => false, 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('dateFinal', DateTimeType::class, array('label' => 'Data do kiedy bedą odbywać się powtarzane wydarzeie', 'mapped' => false, 'required'=>false, 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Utwórz', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
            ->getForm();

        $formTodo->handleRequest($request);

        $dateFinal = $formTodo->get('dateFinal')->getData();
        $dateStart = $formTodo->get('date')->getData();


        if ($formTodo->isSubmitted() && $formTodo->isValid()) {
            if ( $dateFinal > $dateStart) {
                $username = $this->getUser();
                $now = new\DateTime('now');
                $todo->setUser($username);
                $todo->setCreateDate($now);
                $em = $this->getDoctrine()->getManager();
                $em->persist($todo);
                $em->flush();

                $this->addFlash('success', 'Dodano wydarzenie');

                $day = $formTodo->get('day')->getData();

                if ($day > 1) {
                    $dateTemp = clone $todo->getDate();
                    $dateTemp2 = clone $todo->getDateEnd();
                    while (($dateTemp2->modify('+' . $day . 'days')) <= $dateFinal) {
                        $dateTemp->modify('+' . $day . 'days');
                        $newToDo = clone $todo;
                        $newToDo->setDate($dateTemp);
                        $newToDo->setDateEnd($dateTemp2);
                        $em->persist($newToDo);
                        $em->flush();
                    }

                }
                return $this->redirectToRoute('todo_list');


            } else {
                $this->addFlash('danger', 'Zła data zakończenia');
                return $this->redirectToRoute('todo_create');

            }
        }
        return $this->render('Todo/create.html.twig', array(
            'formTodo' => $formTodo->createView()
        ));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')
            ->find($id);

        $formTodo = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('label' => 'Nazwa', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('label' => 'Kategoria', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('label' => 'Priorytet', 'choices' => array('Niska' => 'Niska', 'Średnia' => 'Średnia', 'Wysoka' => 'Wysoka'), 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('label' => 'Opis', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Zapisz zmiany', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
            ->getForm();

        $formTodo->handleRequest($request);

        if ($formTodo->isSubmitted() && $formTodo->isValid()) {


            $em->flush();

            $this->addFlash(
                'notice',
                'Zaktualizowano'
            );

            return $this->redirectToRoute('todo_list');

        }
        return $this->render('Todo/edit.html.twig', array(
            'todo' => $todo,
            'formTodo' => $formTodo->createView()
        ));
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);
        return $this->render('Todo/details.html.twig', array(
            'todo' => $todo
        ));
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')->find($id);

        $em->remove($todo);
        $em->flush();

        $this->addFlash('danger', 'Usunięto wydarzenie');

        return $this->redirectToRoute('todo_list');
    }


}
