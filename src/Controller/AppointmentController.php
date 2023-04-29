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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\AppointmentSearchType; // Ajout de cette ligne
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;




use App\Services\MailService;


class AppointmentController extends AbstractController
{
    #[Route('/Appointment', name: 'app_appointment')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $appointmentRepository = $entityManager->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findAll();
        $appointmentsByMonth = $appointmentRepository->countAppointmentsByMonth();
        $appointmentsByMedecin = $appointmentRepository->countAppointmentsByMedecin();
        $appointmentStatus = $appointmentRepository->countAppointmentsByStatus();
        return $this->render('appointment/index.html.twig', [
            'controller_name' => 'AppointmentController','a' => $appointments,
            'totalAppointments' => $appointmentStatus['total'],
            'confirmedAppointments' => $appointmentStatus['confirmed'],
            'waitingAppointments' => $appointmentStatus['waiting'],
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
        $this->addFlash('success', 'Le rendez-vous a été ajouté avec succès.');

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
        $this->addFlash('success', 'Le rendez-vous a été modifié avec succès.');

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
        $this->addFlash('success', 'Le rendez-vous a été supprimé avec succès.');

        return $this->redirectToRoute('app_appointment');
    }

   

    
    #[Route('/Appointments/search', name: 'appointments_search')]
    public function search(Request $request, AppointmentRepository $repository): Response
    {
        // Créer un formulaire de recherche de rendez-vous
        $form = $this->createForm(AppointmentSearchType::class);

        // Traitement de la soumission de formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            // Rechercher les rendez-vous correspondant aux critères de recherche
            $appointments = $repository->findBySearchCriteria($formData);
        } else {
            // Si le formulaire n'a pas été soumis ou s'il n'est pas valide,
            // initialiser la variable $appointments à une tableau vide
            $appointments = [];
        }

        // Ajouter la condition pour filtrer selon le statut confirmé ou non confirmé
        if (isset($formData['status'])) {
            if ($formData['status'] === true) {
                $appointments = array_filter($appointments, function ($appointment) {
                    $appointment->setStatus(true);
                    return $appointment->isStatus();
                });
            } elseif ($formData['status'] === false) {
                $appointments = array_filter($appointments, function ($appointment) {
                    $appointment->setStatus(false);
                    return !$appointment->isStatus();
                });
            }
        }
        

        return $this->render('appointment/search.html.twig', [
            'form' => $form->createView(),
            'appointments' => $appointments,
        ]);
    }
    #[Route('/appointments/{id}/confirm', name: 'appointments_confirm')]
public function confirm(Request $request,MailService $mailer, Appointment $appointment, EntityManagerInterface $entityManager): Response
{
    $appointment->setStatus(true);
    $entityManager->flush();
    $context=[
        
    ];
   
    $mailer->sendEmail(
       "amenibelhadj556@gmail.com",'Email/email.html.twig',"Confirmation du RDV",$context,
        
            
    
        
        );
        

    

    
    

   

    return $this->redirectToRoute('app_appointment');
}
#[Route('/stat', name: 'stat')]
    public function stat(AppointmentRepository $appointmentRepository): Response
    {
        $appointmentsByMonth = $appointmentRepository->countAppointmentsByMonth();
        $appointmentsByMedecin = $appointmentRepository->countAppointmentsByMedecin();
        $appointmentStatus = $appointmentRepository->countAppointmentsByStatus();
    
        return $this->render('appointment/calendar.html.twig', [
            'appointmentsByMonth' => $appointmentsByMonth,
            'appointmentsByMedecin' => $appointmentsByMedecin,
            'totalAppointments' => $appointmentStatus['total'],
            'confirmedAppointments' => $appointmentStatus['confirmed'],
            'waitingAppointments' => $appointmentStatus['waiting'],
            
        ]);
    }

    #[Route('/cal', name: 'cal')]
    public function cal(AppointmentRepository $appointmentRepository): Response
    {
        $appointments = $appointmentRepository->findAll();
        $appointmentsByMonth = $appointmentRepository->countAppointmentsByMonth();
        $appointmentsByMedecin = $appointmentRepository->countAppointmentsByMedecin();
        $appointmentStatus = $appointmentRepository->countAppointmentsByStatus();
        return $this->render('appointment/cal.html.twig', [
            'controller_name' => 'AppointmentController',
            'appointments' => $appointments,
            'appointmentsByMonth' => $appointmentsByMonth,
            'appointmentsByMedecin' => $appointmentsByMedecin,
            'totalAppointments' => $appointmentStatus['total'],
            'confirmedAppointments' => $appointmentStatus['confirmed'],
            'waitingAppointments' => $appointmentStatus['waiting'],
        ]);
    }

    /**=============================================partie json =============================================================*/






  
  /**  public function indexJSON(EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
    {
        $appointmentRepository = $entityManager->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findAll();

        $jsonContent = $normalizer->normalize($appointments, 'json', ['groups' => 'appointments']);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($jsonContent));

        return $response;
    }*/ 
    
    #[Route('/AppointmentJSON', name: 'app_appointmentJSON')]
    public function getAppointmentsJson(AppointmentRepository $appointmentRepository, SerializerInterface $serializer)
{
    $appointments = $appointmentRepository->findAll();
    $jsonContent = $serializer->serialize($appointments, 'json');

    return new JsonResponse($jsonContent, 200, [], true);
}



#[Route("deleteAppointmentJSON/{id}", name: "deleteAppointmentJSON")]
public function deleteAppointmentJSON($id, NormalizerInterface $normalizer)
{
    $entityManager = $this->getDoctrine()->getManager();
    $appointment = $entityManager->getRepository(Appointment::class)->find($id);

    if (!$appointment) {
        throw $this->createNotFoundException('Aucun rendez-vous trouvé pour l\'id ' . $id);
    }

    $entityManager->remove($appointment);
    $entityManager->flush();

    $jsonContent = $normalizer->normalize($appointment, 'json', ['groups' => 'appointments']);
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $response->setContent(json_encode($jsonContent));

    return $response;
}
#[Route('/appointments/newJSON/{idmedecin}/{idpatient}', name: 'appointments_new2')]
public function newJSON(Request $request, EntityManagerInterface $entityManager, int $idmedecin, int $idpatient, NormalizerInterface $Normalizer): Response
{   
    $medecin = $entityManager->getRepository(User::class)->find($idmedecin);
    $patient = $entityManager->getRepository(User::class)->find($idpatient);

    $appointment = new Appointment();
    $appointment->setIdmedecin($medecin);
    $appointment->setIdpatient($patient);

    // récupération des données de la requête
    $data = json_decode($request->getContent(), true);
    if(isset($data['dateap'])) {
        $appointment->setDateap(new \DateTime($data['dateap']));
    }
    if(isset($data['hour'])) {
        $appointment->setHour($data['hour']);
    }
    $entityManager->persist($appointment);
    $entityManager->flush();

    $jsonContent = $Normalizer->normalize($appointment, 'json', ['groups' => 'appointments']);

    return new Response(json_encode($jsonContent));
}

#[Route('/appointments/{id}/editJSON', name: 'appointments_edit_json', methods: ['PUT'])]
public function editJSON(Request $request, Appointment $appointment, EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
{
    // récupération des données de la requête
    $data = json_decode($request->getContent(), true);

    if(isset($data['dateap'])) {
        $appointment->setDateap(new \DateTime($data['dateap']));
    }
    if(isset($data['hour'])) {
        $appointment->setHour($data['hour']);
    }

    $entityManager->flush();

    $jsonContent = $normalizer->normalize($appointment, 'json', ['groups' => 'appointments']);
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $response->setContent(json_encode($jsonContent));

    return $response;
}















    
    
}
