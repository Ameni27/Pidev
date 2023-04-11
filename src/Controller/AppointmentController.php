<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Appointment;
use App\Entity\User;
use App\Form\AppointmentType; // Ajout de cette ligne
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AppointmentRepository;
class AppointmentController extends AbstractController
{
    #[Route('/Appointment', name: 'app_appointment')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $appointmentRepository = $entityManager->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findAll();
        return $this->render('appointment/index.html.twig', [
            'controller_name' => 'AppointmentController','a' => $appointments
        ]);
    }



    #[Route('/appointments/patient/{id}', name: 'appointments_patient')]
    public function appointmentsByPatient(AppointmentRepository $appointmentRepository, int $id): Response
    {
        $appointments = $appointmentRepository->findBy(['idpatient' => $id]);
    
        return $this->render('appointment/patient.html.twig', [
            'appointments' => $appointments
        ]);
    }
    
    

    
    


    #[Route('/appointments/new/{idmedecin}/{idpatient}', name: 'appointments_new')]
public function new(Request $request, EntityManagerInterface $entityManager, int $idmedecin, int $idpatient): Response
{   
    $medecin = $entityManager->getRepository(User::class)->find($idmedecin);
    $patient = $entityManager->getRepository(User::class)->find($idpatient);

    $appointment = new Appointment();
    $appointment->setIdmedecin($medecin);
    $appointment->setIdpatient($patient);

    $form = $this->createForm(AppointmentType::class, $appointment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($appointment);
        $entityManager->flush();

        return $this->redirectToRoute('appointments_patient', ['id' => 2]);
    }

    return $this->render('appointment/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/appointments/{id}', name: 'appointments_show')]
public function show(Appointment $appointment): Response
{
    return $this->render('appointment/show.html.twig', [
        'appointment' => $appointment,
    ]);
}

#[Route('/appointments/{id}/edit', name: 'appointments_edit')]
public function edit(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(AppointmentType::class, $appointment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_appointment');
    }

    return $this->render('appointment/edit.html.twig', [
        'appointment' => $appointment,
        'form' => $form->createView(),
    ]);
}


#[Route('/appointments/{id}/delete', name: 'appointments_delete')]
    public function delete(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($appointment);
        $entityManager->flush();

        return $this->redirectToRoute('app_appointment');
    }


    
    
}
