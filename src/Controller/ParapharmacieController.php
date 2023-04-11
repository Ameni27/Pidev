<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\Request;
use App\Entity\Parapharmacie;
use App\Entity\Appointment;
use App\Entity\User;
use App\Form\ParapharmacieType; // Ajout de cette ligne
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AppointmentRepository;


class ParapharmacieController extends AbstractController
{
    #[Route('/parapharmacie', name: 'app_parapharmacie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $parapharmacieRepository = $entityManager->getRepository(Parapharmacie::class);
        $parapharmacies = $parapharmacieRepository->findAll();
        return $this->render('parapharmacie/index.html.twig', [
            'controller_name' => 'ParapharmacieController',
            'parapharmacies' => $parapharmacies,
        ]);
    }

    #[Route('/parapharmacie_client', name: 'app_parapharmacie_client')]
    public function indexClient(EntityManagerInterface $entityManager): Response
    {
        $parapharmacieRepository = $entityManager->getRepository(Parapharmacie::class);
        $parapharmacies = $parapharmacieRepository->findAll();
        return $this->render('parapharmacie/client.html.twig', [
            'controller_name' => 'ParapharmacieController',
            'parapharmacies' => $parapharmacies,
        ]);
    }

    #[Route('/Admin', name: 'app_Admin')]
    public function indexHome(): Response
    {
        
        return $this->render('Admin/index.html.twig');
    }

    #[Route('/Home', name: 'app_front')]
    public function indexFront(): Response
    {
        
        return $this->render('templates/Home/index.html.twig');
    }

    #[Route('/parapharmacies/new', name: 'parapharmacies_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parapharmacie = new Parapharmacie();
    
        $form = $this->createForm(ParapharmacieType::class, $parapharmacie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parapharmacie);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_parapharmacie');
        }
    
        return $this->render('parapharmacie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/parapharmacies/{id}', name: 'parapharmacies_show')]
    public function show(Parapharmacie $parapharmacie): Response
{
    return $this->render('parapharmacie/show.html.twig', [
        'parapharmacie' => $parapharmacie,
    ]);
}


#[Route('/parapharmacies/{id}/edit', name: 'parapharmacies_edit')]
public function edit(Request $request, Parapharmacie $parapharmacie, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ParapharmacieType::class, $parapharmacie);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_parapharmacie');
    }

    return $this->render('parapharmacie/edit.html.twig', [
        'parapharmacie' => $parapharmacie,
        'form' => $form->createView(),
    ]);
}
#[Route('/parapharmacies/{id}/delete', name: 'parapharmacies_delete')]
public function delete(Request $request,Parapharmacie $parapharmacie,EntityManagerInterface $entityManager, int $id): Response
{
    $entityManager->remove($parapharmacie);
    $entityManager->flush();

    return $this->redirectToRoute('app_parapharmacie');
}





    

}
