<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;



use Symfony\Component\HttpFoundation\Request;
use App\Entity\Parapharmacie;
use App\Entity\Appointment;
use App\Entity\User;
use App\Form\ParapharmacieType; // Ajout de cette ligne
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



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
            $this->addFlash('success', 'Parapharmacie ajouté avec succès.');
    
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
        $this->addFlash('success', 'Parapharmacie modifiée avec succès avec succès.');

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
    $this->addFlash('success', 'La parapharmacie a été supprimé avec succès.');

    return $this->redirectToRoute('app_parapharmacie');
}


#[Route('/search-parapharmacies', name: 'search_parapharmacies')]
public function search(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $searchTerm = $request->query->get('search');
    $parapharmacieRepository = $entityManager->getRepository(Parapharmacie::class);
    $parapharmacies = [];
    if ($searchTerm !== null) {
        $parapharmacies = $parapharmacieRepository->findBySearchTerm($searchTerm);
    }

    $results = [];
    foreach ($parapharmacies as $parapharmacie) {
        $results[] = [
            'nom' => $parapharmacie->getNom(),
            'adresse' => $parapharmacie->getAdresse(),
        ];
    }

    return $this->json($results);
}

/**
 * @Route("/search", name="search")
 */
public function searchResults(Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
{
    $searchTerm = $request->query->get('q');
    $parapharmacies = [];
    if ($searchTerm !== null) {
        $parapharmacieRepository = $entityManager->getRepository(Parapharmacie::class);
        $parapharmacies = $parapharmacieRepository->findBySearchTerm($searchTerm);
    }

    $results = [];
    foreach ($parapharmacies as $parapharmacie) {
        $results[] = [
            'nom' => $parapharmacie->getNom(),
            'adresse' => $parapharmacie->getAdresse(),
        ];
    }

    $jsonContent = $normalizer->normalize($results, 'json');
    $retour = json_encode($jsonContent);

    return new Response($retour);
}

/**
 * @Route("/search-results", name="search_results")
 */
public function searchResultsPage(): Response
{
    return $this->render('parapharmacie/search_results.html.twig');
}


/**==============================json================================ */
#[Route('/parapharmacieJSON', name: 'app_parapharmacieJSON')]
public function indexJSON(EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
{
    $parapharmacieRepository = $entityManager->getRepository(Parapharmacie::class);
    $parapharmacies = $parapharmacieRepository->findAll();

    if (empty($parapharmacies)) {
        $error = ['error' => 'No parapharmacies found'];
        $json = $serializer->serialize($error, 'json');
    } else {
        $json = $serializer->serialize($parapharmacies, 'json');
    }

    return new JsonResponse($json, 200, [
        'Content-Type' => 'application/json'
    ], true);
}





#[Route('/addParapharmacieJSON/new', name: 'addParapharmacieJSON')]
    public function addParapharmacieJSON(Request $request, NormalizerInterface $normalizer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $parapharmacie = new Parapharmacie();
        $parapharmacie->setNom($request->get('nom'));
        $parapharmacie->setAdresse($request->get('adresse'));
        $parapharmacie->setPhone($request->get('phone'));
    
        $entityManager->persist($parapharmacie);
        $entityManager->flush();
    
        $jsonContent = $normalizer->normalize($parapharmacie, 'json', ['groups' => 'parapharmacies']);
        return new Response(json_encode($jsonContent));
    }


    #[Route('/editParapharmacieJSON/{id}', name: 'editParapharmacieJSON')]
    public function editParapharmacieJSON(Request $request, EntityManagerInterface $entityManager, NormalizerInterface $normalizer, $id): Response
    {
        $parapharmacie = $entityManager->getRepository(Parapharmacie::class)->find($id);
    
        if (!$parapharmacie) {
            return new Response("La parapharmacie avec l'ID $id n'a pas été trouvée.", Response::HTTP_NOT_FOUND);
        }
    
        $nom = $request->get('nom');
        $adresse = $request->get('adresse');
        $phone = $request->get('phone');
    
        if ($nom !== null) {
            $parapharmacie->setNom($nom);
        }
    
        if ($adresse !== null) {
            $parapharmacie->setAdresse($adresse);
        }
    
        if ($phone !== null) {
            $parapharmacie->setPhone($phone);
        }
    
        $entityManager->flush();
    
        $jsonContent = $normalizer->normalize($parapharmacie, 'json', ['groups' => 'parapharmacies']);
        return new Response("Parapharmacie updated successfully " . json_encode($jsonContent));
    }
#[Route("deleteParapharmacieJSON/{id}", name: "deleteParapharmacieJSON")]
public function deleteParapharmacieJSON(Request $request, $id, EntityManagerInterface $entityManager, NormalizerInterface $Normalizer): Response
{
    $parapharmacie = $entityManager->getRepository(Parapharmacie::class)->find($id);

    if (!$parapharmacie) {
        return new Response("Parapharmacie not found for id $id", Response::HTTP_NOT_FOUND);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($parapharmacie);
    $entityManager->flush();

    $jsonContent = $Normalizer->normalize($parapharmacie, 'json', ['groups' => 'parapharmacies']);

    return new Response("Parapharmacie deleted successfully " . json_encode($jsonContent));
}







    

}
