<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Participer;
use App\Entity\UserV;
use App\Form\AssociationType;
use App\Form\RegistrationFormType;
use App\Form\VolontaireType;

use App\Repository\UserVRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;


class UserController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil', methods: ['GET'])]
    public function accueil(UserVRepository $userVRepository): Response
    {
        return $this->render('accueil.html.twig', [
            'associations' => $userVRepository->findByRoleAssociation('ROLE_ASSOCIATION'),
        ]);
    }
    #[Route('/dashboard', name: 'app_admin')]
    public function associationStats(EntityManagerInterface $em): Response
    {

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        $name=$user->getLastname();

        // Si l'utilisateur n'est pas connecté, redirigez ou gérez autrement
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté.");
        }

        // ID de l'association (via l'utilisateur connecté)
        $associationId = $user->getId();

        // Compter le nombre d'événements de l'association
        $eventCountQuery = $em->createQuery(
            "SELECT COUNT(e.id)
         FROM App\Entity\Event e 
         JOIN App\Entity\UserV a 
         WITH a.id = e.userv 
         WHERE e.userv = :id AND   a.roles like :role "
        )->setParameter('id', $associationId)
            ->setParameter('role', '%ROLE_ASSOCIATION%');

        $eventCount = $eventCountQuery->getSingleScalarResult();


        // Compter le nombre de formations de l'association
        $formationCountQuery = $em->createQuery(
            "SELECT COUNT(f.id) 
         FROM App\Entity\Formation f 
         JOIN App\Entity\UserV a 
         WITH a.id = f.userv 
         WHERE f.userv = :id AND a.roles like :role"
        )->setParameter('id', $associationId)
            ->setParameter('role', '%ROLE_ASSOCIATION%');

        $formationCount = $formationCountQuery->getSingleScalarResult();

        $id = $user->getId();

        $eventParticipationStats = $em->createQuery(
            'SELECT e.nameEvent AS eventName, COUNT(p.id) AS participantCount
         FROM App\Entity\Event e
         LEFT JOIN e.participer p
         WHERE e.userv = :userId
         GROUP BY e.id
         ORDER BY participantCount DESC'
        )->setParameter('userId', $id)
            ->setMaxResults(10)
            ->getResult();

        $formationEnrollmentStats = $em->createQuery(
            'SELECT f.titre AS formationName, COUNT(i.id) AS enrollmentCount
         FROM App\Entity\Formation f
         LEFT JOIN f.inscription i
         WHERE f.userv = :userId
         GROUP BY f.id
         ORDER BY enrollmentCount DESC'
        )->setParameter('userId', $id)
            ->setMaxResults(10)
            ->getResult();
        $events = $em->getRepository(Event::class)
            ->findBy(['userv' => $user]);

        // Récupérer les formations associées
        $formations = $em->getRepository(Formation::class)
            ->findBy(['userv' => $user]);

        // Formater les données pour FullCalendar
        $formattedEvents = array_map(function ($event) {
            return [
                'id' => $event->getId(),
                'title' => $event->getNameEvent(),
                'start' => $event->getDateEvent()->format('Y-m-d\TH:i:s'),
                'location' => $event->getLocation(),
                'color' => '#3788d8',
            ];
        }, $events);

        $formattedFormations = array_map(function ($formation) {
            return [
                'id' => $formation->getId(),
                'title' => $formation->getTitre(),
                'start' => $formation->getDateFormation()->format('Y-m-d\TH:i:s'),
                'end' => $formation->getDateFormationFin()->format('Y-m-d\TH:i:s'),
                'duration' => $formation->getDuree(),
                'color' => '#28a745',
            ];
        }, $formations);
        return $this->render('user/dashboard.html.twig', [
            'eventCount' => $eventCount,
            'formationCount' => $formationCount,'id'=>$associationId,
            'eventParticipationStats' => $eventParticipationStats,
            'formationEnrollmentStats' => $formationEnrollmentStats,
            'formattedEvents' => $formattedEvents,
            'formattedFormations' => $formattedFormations,
            'name'=>$name,
        ]);


    }


    #[Route('/edit/{id}', name: 'app_volontaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserV $volontaire, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(VolontaireType::class, $volontaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($volontaire);

            $entityManager->flush();

            return $this->redirectToRoute('app_accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'volontaire' => $volontaire,
            'form' => $form,
        ]);
    }

    #[Route('/editA/{id}', name: 'app_association_edit', methods: ['GET', 'POST'])]
    public function editA(Request $request, UserV $association, EntityManagerInterface $entityManager, FileUploader $fileUploader,UserInterface $user): Response
    {

        $user = $this->getUser();
        $name=$user->getname();
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($association);

            $entityManager->flush();

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/editA.html.twig', [
            'association' => $association,
            'form' => $form,
            'name'=>$name,
        ]);
    }

    #[Route('/dashboard/stat', name: 'app_dashboard_stat')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $id = $user->getId();

        $eventParticipationStats = $entityManager->createQuery(
            'SELECT e.nameEvent AS eventName, COUNT(p.id) AS participantCount
         FROM App\Entity\Event e
         LEFT JOIN e.participer p
         WHERE e.userv = :userId
         GROUP BY e.id
         ORDER BY participantCount DESC'
        )->setParameter('userId', $id)
            ->setMaxResults(10)
            ->getResult();

        $formationEnrollmentStats = $entityManager->createQuery(
            'SELECT f.titre AS formationName, COUNT(i.id) AS enrollmentCount
         FROM App\Entity\Formation f
         LEFT JOIN f.inscription i
         WHERE f.userv = :userId
         GROUP BY f.id
         ORDER BY enrollmentCount DESC'
        )->setParameter('userId', $id)
            ->setMaxResults(10)
            ->getResult();

        return $this->render('user/dashboard.html.twig', [
            'eventParticipationStats' => $eventParticipationStats,
            'formationEnrollmentStats' => $formationEnrollmentStats,
        ]);
    }


    #[Route('/liste_events_part', name: 'app_event_list_part')]
    public function liste_events_part(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        // Fetch events the user participated in along with participation ID and etat
        $events = $em->createQuery("
        SELECT e AS event, p.id AS id, p.etat AS etat
        FROM App\Entity\Event e
        JOIN App\Entity\Participer p WITH p.event = e.id
        WHERE p.userv = :userId
    ")
            ->setParameter('userId', $userId)
            ->getResult();

        // Fetch formations the user is enrolled in along with inscription ID and etat
        $formations = $em->createQuery("
        SELECT f AS formation, i.id AS id, i.etat AS etat
        FROM App\Entity\Formation f
        JOIN App\Entity\Inscription i WITH i.formation = f.id
        WHERE i.userv = :userId
    ")
            ->setParameter('userId', $userId)
            ->getResult();

        return $this->render('user/part_insc.html.twig', [
            'events' => $events,
            'formations' => $formations,
        ]);
    }



    #[Route('/update-etat', name: 'update_etat', methods: ['POST'])]
    public function updateEtat(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $type = $request->request->get('type'); // 'event' or 'formation'
        $id = $request->request->get('id'); // Participer or Inscription ID
        $newEtat = $request->request->get('etat'); // 'actif' or 'non actif'

        if ($type === 'event') {
            $participation = $em->getRepository(Participer::class)->find($id);
        } elseif ($type === 'formation') {
            $participation = $em->getRepository(Inscription::class)->find($id);
        } else {
            throw $this->createNotFoundException('Invalid type.');
        }

        $participation->setEtat($newEtat);
        $em->persist($participation);
        $em->flush();

        return $this->redirectToRoute('app_event_list_part');
    }

    #[Route('/apropos', name: 'apropos')]
    public function apropos(): Response
    {
        return $this->render('apropos.html.twig', []);
    }

    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        return $this->render('test.html.twig', []);
    }

}