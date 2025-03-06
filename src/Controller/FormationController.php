<?php

namespace App\Controller;


use App\Entity\Formation;
use App\Entity\UserV;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formation')]
final class FormationController extends AbstractController
{

    #[Route(name: 'app_formation_index', methods: ['GET'])]
    public function index(Request $request,FormationRepository $formationRepository, EntityManagerInterface $entityManager): Response
    {
        // Get current date
        $currentDate = new \DateTime();
        $searchName = $request->query->get('search_name');
        $dql = 'SELECT e FROM App\Entity\Formation e WHERE e.dateFormation >= :currentDate';
        $parameters = ['currentDate' => $currentDate];

        if (!empty($searchName)) {
            $dql .= ' AND e.titre LIKE :searchName';
            $parameters['searchName'] = $searchName . '%';
        }
        $query = $entityManager->createQuery($dql)->setParameters($parameters);



        $formations = $query->getResult();

        return $this->render('formation/liste_formations.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   $user = $this->getUser();
        $name=$user->getLastname();
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($user) {
            $formation->setAssociation($user);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
            'name'=>$name
        ]);
    }
    #[Route('/liste_formation', name: 'app_formation_list')]
    public function liste_formation(EntityManagerInterface $em){
        $user = $this->getUser();
        $id = $user->getId();
        $name=$user->getLastname();
        $query = $em->createQuery("
        SELECT f 
        FROM App\Entity\Formation f 
        JOIN App\Entity\UserV a WITH a.id = f.userv 
        WHERE f.userv = :id
    ")
            ->setParameter('id', $id)
        ;

        $formations = $query->getResult();
        return $this->render('formation/index.html.twig', ['listeF' => $formations,'name'=>$name]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {   $user = $this->getUser();
        $name=$user->getLastname();
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'name'=>$name
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {   $user = $this->getUser();
        $name=$user->getLastname();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
            'name'=>$name
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->get('_token'))) {
            // Remove related inscriptions
            $inscriptions = $formation->getInscription(); // Assuming you have a OneToMany relationship
            foreach ($inscriptions as $inscription) {
                $entityManager->remove($inscription);
            }

            // Remove the formation
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/inscri', name: 'app_formation_list-inscri')]
    public function listInscri(Formation $formation , EntityManagerInterface $em){
        $id=$formation->getId();
        $user = $this->getUser();
        $name=$user->getLastname();
        $query=$em->createQuery("SELECT v FROM App\Entity\Inscription i join App\Entity\userv v with v.id=i.userv WHERE i.formation = :id")->setParameter('id',$id);
        $inscri=$query->getResult();
        return $this->render('formation/listInscri.html.twig', ['listeVI'=>$inscri,'name'=>$name]);
    }
    #[Route('{id}/liste_f', name: 'app_form_list_volon')]
    public function liste_form_volon(UserV $user, EntityManagerInterface $em){
        $id = $user->getId();
        $query = $em->createQuery("
        SELECT f
        FROM App\Entity\Formation f
        JOIN App\Entity\UserV a WITH a.id = f.userv 
        WHERE a.id = :id
    ")
            ->setParameter('id', $id)
        ;

        $formation = $query->getResult();
        return $this->render('formation/form_vonlon.html.twig', ['listeF' => $formation]);
    }



}
