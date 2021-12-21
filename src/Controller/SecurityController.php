<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/mot-de-passe-oublier", name="app_forgotten_password")
     * 
     * @IsGranted("ROLE_SUPADMIN")
     */
    public function forgetPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator): Response
    {
        // On initialise le formulaire
        $form = $this->createForm(ResetPasswordFormType::class);
    
        // On traite le formulaire
        $form->handleRequest($request);
    
        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();
    
            // On cherche un utilisateur ayant cet e-mail
            $user = $userRepository->findOneBy(['email' => $donnees['email']]);
    
            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue.');
                
                // On retourne sur la page de connexion
                return $this->redirectToRoute('app_forgotten_password');
            }
    
            // On génère un token
            $token = $tokenGenerator->generateToken();
    
            // On essaie d'écrire le token en base de données
            try{
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_forgotten_password');
            }
    
            // On redirige vers la page de changement de mot de passe
            return $this->redirectToRoute('app_reset_password', ['token' => $user->getResetToken()]);
        }
    
        // On envoie le formulaire à la vue
        return $this->render('security/forgotten_password.html.twig',[
            'emailForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/reinitialisation-mot-de-passe/{token}", name="app_reset_password")
     * 
     * @IsGranted("ROLE_SUPADMIN")
     */
    public function resetPassword(Request $request, string $token, UserRepository $userRepository, UserPasswordHasherInterface $passwordEncoder)
    {
        // On cherche un utilisateur avec le token donné
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Utilisateur inconnu');
            return $this->redirectToRoute('app_forgotten_password');
        }

        // Si le formulaire est envoyé en méthode post
        if ($request->isMethod('POST')) {
            // On supprime le token
            $user->setResetToken(null);
            
            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->hashPassword($user, $request->request->get('password')));
            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On crée le message flash
            $this->addFlash('success', 'Mot de passe mis à jour pour ' . $user->getEmail());

            // On redirige vers la page de connexion
            return $this->redirectToRoute('app_forgotten_password');

        }else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', [
                'token' => $token,
                'user' => $user,
            ]); 
        }
    }

    /**
     * @Route("/ajout-nouvel-utilisateur", name="app_register")
     * 
     * @IsGranted("ROLE_SUPADMIN")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHashed): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($passwordHashed->hashPassword($user, $user->getPassword()));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // generate a success message
            $this->addFlash('success','Un nouvel utilisateur a été enregistré');
            // redirect to current page
            return $this->redirectToRoute('app_register');
        }

        return $this->render('security/register_user.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}
