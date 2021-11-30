<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Student;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StudentController extends AbstractController
{
    /**
    * @Route("/student/new", name="student_new_form", methods={"POST", "GET"})
    */
    public function new(Request $request)
    {
        $student = new Student();
        // create a form with 'firstName' and 'surname' text fields
        $form = $this->createFormBuilder($student)
            ->add('firstName', TextType::class)
            ->add('surname', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Student'))
            ->getForm();
        // if was POST submission, extract data and put into '$student'
        $form->handleRequest($request);
        // if SUBMITTED & VALID - go ahead and create new object
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->createAction($student);
        }
        
        $template = 'student/new.html.twig';
        $args = [
            'form' => $form->createView(),
        ];
        return $this->render($template, $args);
    }
    
    /**
     * @Route("/student", name="student_list")
     */
    public function listAction()
    {
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');
        $students = $studentRepository->findAll();
        
        $template = 'student/list.html.twig';

        $args = [
            'students' => $students,
        ];

        return $this->render($template, $args);
    }

    /**
     * @Route("/student/{id}", name="student_show")
     */
    public function show(Student $student)
    {
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');
        $template = 'student/show.html.twig';
        if (!$student) {
            $template = 'error/404.html.twig';
        }
        $args = [
            'student' => $student
        ];
        return $this->render($template, $args);
    }

    /**
    * @Route("/student/create/{firstName}/{surname}")
    */
    private function create($firstName, $surname)
    {
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setSurname($surname);
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return $this->redirectToRoute('student_list');

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);
        return new Response('Created new student with id '.$student->getId());
    }
    public function createAction(Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return $this->redirectToRoute('student_list');
    }

    /**
    * @Route("/student/delete/{id}")
    */
    public function delete(Student $student)
    {
        // entity manager
        $em = $this->getDoctrine()->getManager();
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');
        // find thge student with this ID
        $id = $student->getId();
        // tells Doctrine you want to (eventually) delete the Student (no queries yet)
        $em->remove($student);
        // actually executes the queries (i.e. the DELETE query)
        $em->flush();
        return new Response('Deleted student with id '.$id);
    }

    /**
    * @Route("/student/update/{id}/{newFirstName}/{newSurname}")
    */
    public function update(Student $student, $newFirstName, $newSurname)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $student->getId();
        if (!$student) {
            throw $this->createNotFoundException(
                'No student found for id '.$id
            );
        }
        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);
        $em->flush();
        return $this->redirectToRoute('student_show', [
            'id' => $id
        ]);
    }
}