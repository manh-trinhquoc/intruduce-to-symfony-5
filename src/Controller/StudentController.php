<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Student;
use App\Repository\StudentRepository;

class StudentController extends AbstractController
{
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
    public function create($firstName, $surname)
    {
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setSurname($surname);
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);
        return new Response('Created new student with id '.$student->getId());
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