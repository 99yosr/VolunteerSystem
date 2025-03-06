<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inscription')]
final class InscriptionController extends AbstractController
{
    #[Route(name: 'app_inscription_index', methods: ['GET'])]
    public function index(InscriptionRepository $inscriptionRepository): Response
    {
        return $this->render('inscription/index.html.twig', [
            'inscriptions' => $inscriptionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inscription);
            $entityManager->flush();

            return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscription/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inscription_delete', methods: ['POST'])]
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscription->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inscription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/insc/{formationId}', name: 'app_inscription_create', methods: ['GET', 'POST'])]
    public function createParticipation($formationId, EntityManagerInterface $entityManager): Response
    {
        // user li 3amel login
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_VOLONTAIRE', $user->getRoles())) {
            throw $this->createAccessDeniedException('You must be logged in as a volunteer to participate.');
        }




        // select events avec query
        $formation = $entityManager->createQuery(
            "SELECT f FROM App\Entity\Formation f WHERE f.id = :id"
        )
            ->setParameter('id', $formationId)
            ->getOneOrNullResult();

        if (!$formation) {
            throw $this->createNotFoundException('Event not found.');
        }

        // test user appartient ou non
        $existingInscription = $entityManager->getRepository(Inscription::class)
            ->findOneBy(['userv' => $user, 'formation' =>$formation]);

        if ($existingInscription) {
            $this->addFlash('warning', 'You are already participating in this formation.');
            return $this->redirectToRoute('app_formation_index');
        }

        // nouveau participant
        $inscrire = new Inscription();
        $inscrire->setVolontaire($user);
        $inscrire->setFormation($formation);
        $inscrire->setDateI(new \DateTime('now'));
        $inscrire->setEtat('actif');

        $entityManager->persist($inscrire);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully registered for the event.');
        return $this->redirectToRoute('app_event_list_part');
    }
}
