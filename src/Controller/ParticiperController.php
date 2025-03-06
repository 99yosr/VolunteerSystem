<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participer;
use App\Form\ParticiperType;
use App\Repository\EventRepository;
use App\Repository\ParticiperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participer')]
class ParticiperController extends AbstractController
{

    #[Route(name: 'app_participer_index', methods: ['GET'])]
    public function index(ParticiperRepository $participerRepository): Response
    {
        return $this->render('participer/index.html.twig', [
            'participers' => $participerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_participer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participer = new Participer();
        $form = $this->createForm(ParticiperType::class, $participer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participer);
            $entityManager->flush();

            return $this->redirectToRoute('app_participer_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('participer/new.html.twig', [
            'participer' => $participer,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_participer_show',requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function show(Participer $participer): Response
    {
        return $this->render('participer/show.html.twig', [
            'participer' => $participer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participer $participer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticiperType::class, $participer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participer/edit.html.twig', [
            'participer' => $participer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participer_delete', methods: ['POST'])]
    public function delete(Request $request, Participer $participer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participer->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participer_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/par/{eventId}', name: 'app_participer_create', methods: ['GET', 'POST'])]
    public function createParticipation($eventId, EventRepository $er, EntityManagerInterface $entityManager): Response
    {
        // user li 3amel login
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_VOLONTAIRE', $user->getRoles())) {
            throw $this->createAccessDeniedException('You must be logged in as a volunteer to participate.');
        }

        $event = $er->find($eventId);


        // select events avec query
        /*$event = $entityManager->createQuery(
            "SELECT e FROM App\Entity\Event e WHERE e.id = :id"
        )
            ->setParameter('id', $eventId)
            ->getOneOrNullResult();*/

        if (!$event) {
            throw $this->createNotFoundException('Event not found.');
        }

        // test user appartient ou non
        $existingParticipation = $entityManager->getRepository(Participer::class)
            ->findOneBy(['userv' => $user, 'event' => $event]);

        if ($existingParticipation) {
            $this->addFlash('warning', 'You are already participating in this event.');
            return $this->redirectToRoute('app_event_index');
        }

        // nouveau participant
        $participer = new Participer();
        $participer->setVolontaire($user);
        $participer->setEvent($event);
        $participer->setEtat('actif');

        $entityManager->persist($participer);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully registered for the event.');
        return $this->redirectToRoute('app_event_index');
    }

}
