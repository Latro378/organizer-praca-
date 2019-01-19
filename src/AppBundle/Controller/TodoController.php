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
            ->add('day', IntegerType::class, array('label' => 'co ile dni', 'mapped' => false, 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('dateEnd', DateTimeType::class, array('label' => 'do kiedy', 'mapped' => false, 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('label' => 'Priorytet', 'choices' => array('Niska' => 'Niska', 'Średnia' => 'Średnia', 'Wysoka' => 'Wysoka'), 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('label' => 'Opis', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('date', DateTimeType::class, array('label' => 'Data', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Utwórz', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
            ->getForm();

        $formTodo->handleRequest($request);

        if ($formTodo->isSubmitted() && $formTodo->isValid()) {
            $username = $this->getUser();
            $now = new\DateTime('now');
            $todo->setUser($username);
            $todo->setCreateDate($now);
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            $this->addFlash(
                'notice',
                'Dodano'
            );
            $day = $formTodo->get('day')->getData();
            $dateEnd = $formTodo->get('dateEnd')->getData();
            if ($day > 1) {
                $dateTemp = clone $todo->getDate();
                while (($dateTemp->modify('+' . $day . 'days')) <= $dateEnd){
                $newToDo = clone $todo;
                $newToDo->setDate($dateTemp);
                $em->persist($newToDo);
                $em->flush();
                }
            }
            return $this->redirectToRoute('todo_list');

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
            ->add('date', DateTimeType::class, array('label' => 'Data', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
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

        $this->addFlash(
            'notice',
            'Usunięto'
        );

        return $this->redirectToRoute('todo_list');
    }


}
