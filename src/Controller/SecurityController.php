<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Services\Mailer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class SecurityController extends AbstractController
{    
    /**
     * @Route("/", name="security_login")
     */
    public function login(AuthenticationUtils $utils){
        
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('robot');
        }

        $user = new User();
        return $this->render('security/login.html.twig',[
            "lastUsername" => $utils->getLastUsername(),
            "error" => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout() {}

    /**
     * @Route("/forgottenPassword", name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
            

            if ($user === null) {
                $this->addFlash('danger', 'Cette adresse n\'existe pas');
                return $this->redirectToRoute('security_login');
            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $user->setPasswordRequestedAt(new \Datetime());
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('security_login');
            }

            $bodyMail = $mailer->createBodyMail('security/mail.html.twig', [
                'user' => $user
            ]);
            $mailer->sendMessage('jumeau.numerique@gmail.com', $user->getEmail(), 'Réinitialisation du mot de passe', $bodyMail);

            $this->addFlash('success', 'Un mail va vous être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien que vous recevrez sera valide 1h.');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/security_login');
        
    }

    /**
     * @Route("/{id}/{token}", name="app_reset_password")
     */
    public function resetPassword(User $user, Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        /*$entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);*/

        if ($user->getResetToken() === null || $token !== $user->getResetToken() || !$this->isRequestInTime($user->getPasswordRequestedAt())) {
            $this->addFlash('danger', 'Le lien est erroné ou a expiré');
            return $this->redirectToRoute('security_login');
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            
            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setResetToken(null);
            $user->setPasswordRequestedAt(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour');

            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);

    }

    // si supérieur à 1h, retourne false
    // sinon retourne true
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;        
        }
        
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 60;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }
}
