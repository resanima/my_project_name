<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ToDoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction()
    {
        
        $todos = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->findAll();

        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig', [

            'todos'=>$todos

            ]);
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo;

        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, ['attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('category', TextType::class, ['attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('description', TextareaType::class, ['attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('priority', ChoiceType::class, ['choices'=>['Low'=>'Low','Normal'=>'Normal','High'=>'High'], 'attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('dueDate', DateTimeType::class, ['attr'=>['class'=>'formcontrol', 'style'=>'margin-bottom: 15px' ]])
                ->add('save', SubmitType::class, ['label'=>'Create Todo', 'attr'=>[
                    'class'=>'btn btn-primary','style'=>'margin-bottom']])
                ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $name           = $form['name']->getData();
            $category       = $form['category']->getData();
            $description    = $form['description']->getData();
            $priority       = $form['priority']->getData();
            $due_date       = $form['dueDate']->getData();

            $now = new \DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice', 'Todo Added'

                );

            return $this->redirectToRoute('todo_list');
        }

        // replace this example code with whatever you need
        return $this->render('todo/create.html.twig', [

            'form' => $form->createView()

            ]);
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->find($id);

        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, ['attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('category', TextType::class, ['attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('description', TextareaType::class, ['attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('priority', ChoiceType::class, ['choices'=>['Low'=>'Low','Normal'=>'Normal','High'=>'High'], 'attr'=>['class'=>'form-control', 'style'=>'margin-bottom: 15px' ]])
                ->add('dueDate', DateTimeType::class, ['attr'=>['class'=>'formcontrol', 'style'=>'margin-bottom: 15px' ]])
                ->add('save', SubmitType::class, ['label'=>'Update Todo', 'attr'=>[
                    'class'=>'btn btn-primary','style'=>'margin-bottom']])
                ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $name           = $form['name']->getData();
            $category       = $form['category']->getData();
            $description    = $form['description']->getData();
            $priority       = $form['priority']->getData();
            $due_date       = $form['dueDate']->getData();

            $now = new \DateTime('now');
            
            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);
            
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);

            $em->flush();

            $this->addFlash(
                'notice', 'Todo Updated'

                );
            
            return $this->redirectToRoute('todo_list');
        }
        
        // replace this example code with whatever you need
        return $this->render('todo/edit.html.twig',[
            
            'todo'=>$todo, 'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id){
        
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->find($id);
        
        // replace this example code with whatever you need
        return $this->render('todo/details.html.twig', [
            
            'todo'=>$todo
        ]);
    }
    
    /**
     * @Route("/todo/delete/{id}", name="delete")
     */
    public function deleteAction($id){
        
        $em = $this->getDoctrine()->getManager();
        
        $todo = $em->getRepository('AppBundle:Todo')->find($id);
        
        $em->remove($todo);
        $em->flush();
        
        $this->addFlash('notice', 'todo removed');
        
        return $this->redirectToRoute("todo_list");
    }
}
