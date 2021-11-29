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
    public function list()
    {
        $studentRepository = new StudentRepository();
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
    public function show($id)
    {
        $studentRepository = new StudentRepository();
        $student = $studentRepository->findOne($id);
        $template = 'student/show.html.twig';
        if (!$student) {
            $template = 'error/404.html.twig';
        }
        $args = [
        'student' => $student
        ];
        return $this->render($template, $args);
    }
}