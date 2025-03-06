<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\UserV;
use App\Form\EventEditType;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//use Nucleos\DomPDFBundle\Service\Pdf;
use App\Service\PdfGeneratorService;



#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        $currentDate = new \DateTime();
        $searchName = $request->query->get('search_name');

        $dql = 'SELECT e FROM App\Entity\Event e WHERE e.dateEvent >= :currentDate';
        $parameters = ['currentDate' => $currentDate];

        if (!empty($searchName)) {
            $dql .= ' AND e.nameEvent LIKE :searchName';
            $parameters['searchName'] = $searchName . '%';
        }
        $query = $entityManager->createQuery($dql)->setParameters($parameters);

        $events = $query->getResult();

        return $this->render('event/liste_events.html.twig', [
            'events' => $events,
        ]);
    }


    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,FileUploader $fileUploader): Response
    {   $user = $this->getUser();
        $name=$user->getLastname();
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $user = $this->getUser();
        $imgFile = $form->get('image')->getData();
        if( $imgFile){
            $imgFileName = $fileUploader->upload($imgFile);
            $event->setImage($imgFileName);
        }
        if ($user) {
            $event->setAssociation($user);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
            'name'=>$name,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show',requirements: ['id'=>'\d+'], methods: ['GET'] )]
    public function show(Event $event): Response
    {   $user = $this->getUser();
        $name=$user->getLastname();
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'name'=>$name,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $name=$user->getLastname();
        $form = $this->createForm(EventEditType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
            'name'=>$name,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->get('_token'))) {
            // Remove related participer entities
            $participers = $event->getParticiper(); // Assuming a OneToMany relationship
            foreach ($participers as $participer) {
                $entityManager->remove($participer);
            }

            // Remove the event
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_list', [], Response::HTTP_SEE_OTHER);
    }

#[Route('/{id}/volon', name: 'app_event_list-parti')]
    public function listParti(Event $event , EntityManagerInterface $em){
        $user = $this->getUser();
        $name=$user->getLastname();
        $id=$event->getId();
        $query=$em->createQuery("SELECT v FROM App\Entity\Participer p join App\Entity\UserV v with v.id=p.userv WHERE p.event = :id")->setParameter('id',$id);
        $participant=$query->getResult();
        return $this->render('event/listParti.html.twig', ['listeV'=>$participant,'name'=>$name]);
}

    #[Route('/liste_events', name: 'app_event_list')]
    public function liste_events(EntityManagerInterface $em){
        $user = $this->getUser();
        $id = $user->getId();
        $name=$user->getLastname();
        // Modify the query to filter by ROLE_ASSOCIATION
        $query = $em->createQuery("
        SELECT e 
        FROM App\Entity\Event e 
        JOIN App\Entity\UserV a WITH a.id = e.userv 
        WHERE e.userv = :id
    ")
            ->setParameter('id', $id)
        ;

        $events = $query->getResult();
        return $this->render('event/index.html.twig', ['listeE' => $events,'name'=>$name]);
    }
    #[Route('{id}/liste_ev', name: 'app_event_list_volon')]
    public function liste_events_volon(Userv $user ,EntityManagerInterface $em){
        $id=$user->getId();
        $query = $em->createQuery("
        SELECT e 
        FROM App\Entity\Event e 
        JOIN App\Entity\UserV a WITH a.id = e.userv 
        WHERE a.id = :id
    ")
            ->setParameter('id', $id)
        ;

        $events = $query->getResult();
        return $this->render('event/event_vonlon.html.twig', ['listeEv' => $events]);
    }

    #[Route('/{id}/flyer', name: 'app_event_flyer')]
    public function generateFlyer(Event $event, PdfGeneratorService $pdf): Response
    {

        $user = $this->getUser();
        $imagePath1 = realpath($this->getParameter('kernel.project_dir') . '/public/img/flyer_back.jpg');
        $imagePath1 = str_replace('/', DIRECTORY_SEPARATOR, $imagePath1);

        $imageData1 = base64_encode(file_get_contents($imagePath1));


        // Récupérer les détails de l'événement
        $imagePath = realpath($this->getParameter('kernel.project_dir') . '/public/uploads/' . $user->getImage());

        $imagePath = str_replace('/', DIRECTORY_SEPARATOR, $imagePath);
        // Vérifier si le fichier existe
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
    } else {
        echo "Erreur : Le fichier est introuvable au chemin => $imagePath";
    }
        $flyerData = [
            'title' => $event->getNameEvent(),
            'description' => $event->getDescription(),
            'date' => $event->getDateEvent()->format('d-m-Y'),
            'location' => $event->getLocation(),
            'image' => 'data:image/jpeg;base64,' . $imageData,
            'contact' => $user->getEmail(),
            'background' => 'data:image/jpg;base64,' . $imageData1,
        ];
        // Générer le contenu HTML du flyer
        $htmlContent = $this->renderView('event/flyer.html.twig', [
            'flyerData' => $flyerData,
        ]);

        // Générer le fichier PDF
       $pdfOutput = $pdf->generate($htmlContent);

        // Retourner le PDF en réponse
        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="flyer_event.pdf"',
        ]);
    }

}
