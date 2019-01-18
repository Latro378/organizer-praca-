<?php

namespace AppBundle\Controller;


use AppBundle\AppBundle;
use AppBundle\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
        $username = $this->getUser()->getUsername();
        $todos = $this->getDoctrine()->getManager()
            ->getRepository(Todo::class)->findBy([
                'userId' => $username
            ]);


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
            ->add('name', TextType::class, array('label' => 'Nazwa','attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('label' => 'Kategoria','attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('label' => 'Priorytet','choices' => array('Niska' => 'Niska','Średnia' => 'Średnia', 'Wysoka' => 'Wysoka'),'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('label' => 'Opis', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('date', DateTimeType::class, array('label'=>'Data', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Utwórz', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
            ->getForm();


        $formTodo -> handleRequest($request);

        if( $formTodo -> isSubmitted() && $formTodo -> isValid()){

            $name = $formTodo['name'] -> getData();
            $category = $formTodo['category'] -> getData();
            $description = $formTodo['description'] -> getData();
            $priority = $formTodo['priority'] -> getData();
            $date = $formTodo['date'] -> getData();

            $username = $this->getUser()->getUsername();
            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDate($date);
            $todo->setUserId($username);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice',
                'Dodano'
            );

            return $this->redirectToRoute('todo_list');

        }
        return $this->render('Todo/create.html.twig',array(
         'formTodo' => $formTodo->createView()
        ));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {

        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

        $username = $this->getUser()->getUsername();
        $now = new\DateTime('now');

        $todo->setName($todo->getName());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription($todo->getDescription());
        $todo->setPriority($todo->getPriority());
        $todo->setDate($todo->getDate());
        $todo->setUserId($username);
        $todo->setCreateDate($now);

        $formTodo = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('label' => 'Nazwa','attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('label' => 'Kategoria','attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('label' => 'Priorytet','choices' => array('Niska' => 'Niska','Średnia' => 'Średnia', 'Wysoka' => 'Wysoka'),'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('label' => 'Opis', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('date', DateTimeType::class, array('label'=>'Data', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Zapisz zmiany', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
            ->getForm();


        $formTodo -> handleRequest($request);

        if( $formTodo -> isSubmitted() && $formTodo -> isValid()) {

            $name = $formTodo['name']->getData();
            $category = $formTodo['category']->getData();
            $description = $formTodo['description']->getData();
            $priority = $formTodo['priority']->getData();
            $date = $formTodo['date']->getData();

            //$username = $this->getUser()->getUsername();
            $now = new\DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDate($date);
            $todo->setUserId($username);
            $todo->setCreateDate($now);


            $em->flush();

            $this->addFlash(
                'notice',
                'Zaktualizowano'
            );

            return $this->redirectToRoute('todo_list');

        }
        return $this->render('Todo/edit.html.twig', array(
            'todo' =>$todo,
            'formTodo' =>$formTodo->createView()
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
        return $this->render('Todo/view.html.twig', array(
            'todo' =>$todo
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
