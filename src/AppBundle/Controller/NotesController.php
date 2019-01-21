<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Notes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotesController extends Controller
{
    /**
     * @Route("/note", name="note_list")
     */
    public function listNotesAction()
    {

        $username = $this->getUser()->getUsername();
        $notes = $this->getDoctrine()->getManager()
            ->getRepository(Notes::class)->findBy([
                'username' => $username
            ]);

        return $this->render('Notes/notes.html.twig', array(
            'notes' => $notes
        ));

    }


    /**
     * @Route("/note/create", name="note_create")
     */
    public function createAction(Request $request)
    {
        $note = new Notes();

        $formNotes = $this->createFormBuilder($note)
            ->add('name', TextType::class, array('label' => 'Nazwa notatki', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('content', TextareaType::class, array('label' => 'Tekst', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Zapisz', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
            ->getForm();


        $formNotes->handleRequest($request);

        if ($formNotes->isSubmitted() && $formNotes->isValid()) {

            $name = $formNotes['name']->getData();
            $content = $formNotes['content']->getData();

            $username = $this->getUser()->getUsername();
            $now = new\DateTime('now');

            $note->setName($name);
            $note->setContent($content);
            $note->setUsername($username);
            $note->setCreateDateNotes($now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();

            $this->addFlash(
                'notice',
                'Dodano notatkę'
            );

            return $this->redirectToRoute('note_list');

        }
        return $this->render('Notes/create.html.twig', array(
            'formNotes' => $formNotes->createView()
        ));
    }

    /**
     * @Route("/note/edit/{id}", name="note_edit")
     */
    public function editAction($id, Request $request)
    {

        $note = $this->getDoctrine()
            ->getRepository('AppBundle:Notes')
            ->find($id);

        $username = $this->getUser()->getUsername();
        $now = new\DateTime('now');

        $note->setName($note->getName());
        $note->setContent($note->getContent());
        $note->setUsername($username);
        $note->setCreateDateNotes($now);

        $formNotes = $this->createFormBuilder($note)
            ->add('name', TextType::class, array('label' => 'Nazwa notatki', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('content', TextareaType::class, array('label' => 'Tekst', 'attr' => array('class' => 'form_control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Zapisz zmiany', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 15px')))
            ->getForm();


        $formNotes->handleRequest($request);

        if ($formNotes->isSubmitted() && $formNotes->isValid()) {

            $name = $formNotes['name']->getData();
            $content = $formNotes['content']->getData();

            //$username = $this->getUser()->getUsername();
            $now = new\DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $note = $em->getRepository('AppBundle:Notes')->find($id);

            $note->setName($name);
            $note->setContent($content);
            $note->setUsername($username);
            $note->setCreateDateNotes($now);


            $em->flush();

            $this->addFlash(
                'notice',
                'Zaktualizowano notatkę'
            );

            return $this->redirectToRoute('note_list');

        }
        return $this->render('Notes/edit.html.twig', array(
            'note' => $note,
            'formNotes' => $formNotes->createView()
        ));
    }

    /**
     * @Route("/note/view/{id}", name="note_view")
     */
    public function viewAction($id)
    {
        $note = $this->getDoctrine()
            ->getRepository('AppBundle:Notes')
            ->find($id);
        return $this->render('Notes/view.html.twig', array(
            'note' => $note
        ));
    }

    /**
     * @Route("/note/delete/{id}", name="note_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository('AppBundle:Notes')->find($id);

        $em->remove($note);
        $em->flush();

        $this->addFlash(
            'notice',
            'Usunięto notatkę'
        );

        return $this->redirectToRoute('note_list');
    }

}
