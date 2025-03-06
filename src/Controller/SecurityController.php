<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(UserInterface $user): Response
    {    $user = $this->getUser();
        $name=$user->getLastname();
        // Vérifier le rôle de l'utilisateur
        if ($this->isGranted('ROLE_ASSOCIATION')) {
            // Si l'utilisateur est une association, utiliser un template spécifique pour ce rôle
            return $this->render('user/profileAss.html.twig', [
                'user' => $user,
                'name'=>$name,
            ]);
        } elseif ($this->isGranted('ROLE_VOLONTAIRE')) {
            // Si l'utilisateur est un volontaire, utiliser un autre template spécifique
            return $this->render('user/profile.html.twig', [
                'user' => $user,
            ]);
        }

        // Optionnel : gérer un cas par défaut si l'utilisateur n'a pas un des rôles attendus
        throw $this->createNotFoundException('Role non trouvé');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
