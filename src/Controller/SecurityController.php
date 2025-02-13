<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe de base pour les contrôleurs Symfony
use Symfony\Component\HttpFoundation\Response; // Classe pour gérer les réponses HTTP
use Symfony\Component\Routing\Attribute\Route; // Annotation pour définir des routes
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; // Classe pour gérer l'authentification

/**
 * Contrôleur responsable de la gestion de l'authentification des utilisateurs.
 */
class SecurityController extends AbstractController
{
    /**
     * Route pour afficher la page de connexion de l'utilisateur.
     * 
     * @param AuthenticationUtils $authenticationUtils Utilitaire pour récupérer les erreurs d'authentification et le dernier nom d'utilisateur saisi.
     * @return Response Retourne la page de connexion avec les éventuelles erreurs.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifie si l'utilisateur est déjà connecté, si oui, il peut être redirigé vers une autre page.
        // Cette ligne est commentée mais peut être activée pour éviter qu'un utilisateur connecté puisse accéder à la page de connexion.
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path'); // Redirection vers une autre route si nécessaire
        // }

        // Récupération de la dernière erreur d'authentification s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Récupération du dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affichage du formulaire de connexion avec le dernier nom d'utilisateur et l'erreur (s'il y en a une)
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, // Pré-remplit le champ username avec la dernière valeur entrée
            'error' => $error, // Affiche un message d'erreur en cas de tentative échouée
        ]);
    }

    /**
     * Route pour la déconnexion de l'utilisateur.
     * 
     * Cette méthode ne contient pas de logique explicite car la déconnexion est gérée par Symfony via le firewall.
     * Une exception est levée car cette méthode ne doit jamais être exécutée directement.
     * 
     * @throws \LogicException
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette exception est levée intentionnellement car la déconnexion est interceptée par le firewall de Symfony.
        throw new \LogicException('Cette méthode peut rester vide - elle sera interceptée par le firewall de sécurité.');
    }
}
