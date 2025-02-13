<?php

namespace App\Controller;

use App\Entity\User; // Importation de la classe User, qui représente un utilisateur dans la base de données
use App\Form\RegistrationFormType; // Importation du type de formulaire d'inscription
use App\Security\AppAuthenticator; // Importation de l'authentificateur pour gérer l'authentification (non utilisé ici)
use App\Security\EmailVerifier; // Importation du service EmailVerifier qui permet de gérer la vérification des emails
use Doctrine\ORM\EntityManagerInterface; // Importation de l'interface de gestion d'entités avec Doctrine
use Symfony\Bridge\Twig\Mime\TemplatedEmail; // Importation de la classe permettant d'envoyer des emails avec un template Twig
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base des contrôleurs Symfony
use Symfony\Bundle\SecurityBundle\Security; // Gestion de la sécurité avec Symfony
use Symfony\Component\HttpFoundation\Request; // Gestion des requêtes HTTP
use Symfony\Component\HttpFoundation\Response; // Gestion des réponses HTTP
use Symfony\Component\Mime\Address; // Classe pour définir l'adresse des emails
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Interface pour gérer le hachage des mots de passe
use Symfony\Component\Routing\Attribute\Route; // Permet de définir des routes directement dans les méthodes
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface; // Gestion des exceptions liées à la vérification des emails

class RegistrationController extends AbstractController
{
    // Injection de dépendances : Le service EmailVerifier est passé en paramètre au constructeur.
    // Ce service est utilisé pour envoyer l'email de vérification.
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    // Route pour la page d'inscription de l'utilisateur. 
    // Cette méthode permet à un utilisateur de s'inscrire en remplissant un formulaire.
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Création d'un objet User pour stocker les données du formulaire
        $user = new User();
        
        // Création du formulaire d'inscription à partir du formulaire RegistrationFormType
        $form = $this->createForm(RegistrationFormType::class, $user);
        
        // Gestion des données envoyées par le formulaire
        $form->handleRequest($request);

        // Si le formulaire a été soumis
        if ($form->isSubmitted()) {
            // Vérification de la validité du formulaire
            if (!$form->isValid()) {
                dump($form->getErrors(true, false)); // Affichage des erreurs pour déboguer si nécessaire
                die; // Arrêt du processus pour déboguer (ne doit pas être présent en production)
            }

            // Récupération du mot de passe en clair et hachage de celui-ci
            $plainPassword = $form->get('plainPassword')->getData(); // Récupération du mot de passe en clair
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword)); // Hachage du mot de passe avant de le sauvegarder

            // Persistance de l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi de l'email de confirmation après l'inscription
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail()) // Création d'un email avec un template Twig
                    ->from(new Address('michael.ditlecadet@etu.univ-amu.fr', 'LetterBox')) // Définition de l'expéditeur de l'email
                    ->to((string) $user->getEmail()) // Destinataire : l'email de l'utilisateur
                    ->subject('Please Confirm your Email') // Sujet de l'email
                    ->htmlTemplate('registration/confirmation_email.html.twig') // Template Twig pour le corps de l'email
            );

            // Redirection après inscription réussie
            return $this->redirectToRoute('app_home'); // Redirige l'utilisateur vers la page d'accueil
        }

        // Rendu du formulaire d'inscription dans la vue
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form, // Passage du formulaire à la vue Twig
        ]);
    }

    // Route pour la vérification de l'email. L'utilisateur doit cliquer sur le lien reçu pour vérifier son email.
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        // Empêche l'accès à cette page si l'utilisateur n'est pas entièrement authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Tentative de validation du lien de confirmation d'email
        try {
            /** @var User $user */
            $user = $this->getUser(); // Récupère l'utilisateur actuellement connecté
            $this->emailVerifier->handleEmailConfirmation($request, $user); // Traite la confirmation de l'email
        } catch (VerifyEmailExceptionInterface $exception) {
            // Si une erreur survient lors de la vérification de l'email, un message d'erreur est affiché
            $this->addFlash('verify_email_error', $exception->getReason()); // Flash message pour l'exception

            // Redirection vers la page d'inscription en cas d'erreur
            return $this->redirectToRoute('app_register');
        }

        // Ajout d'un message flash indiquant que l'email a été vérifié avec succès
        $this->addFlash('success', 'Votre email a bien été vérifié.');

        // Redirection vers la page d'accueil après la vérification de l'email
        return $this->redirectToRoute('app_home');
    }
}
