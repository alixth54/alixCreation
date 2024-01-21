<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Adress;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        $user = new User();
    $adress = new adress();
    $user->getAdresses()->add($adress);

        $form = $this->createForm(RegistrationFormType::class, $user);
       
    
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );


            $entityManager->persist($user);
            // $entityManager->flush();
            
            foreach ($user->getAdresses() as $adress) {
                $adress->setUserId($user);
                 
                $entityManager->persist($adress);
            }
        
            $entityManager->flush();

            $transport = new EsmtpTransport('smtp.google.com');
                        $transport->setUsername('eseht.xila@gmail.com');
                        $transport->setPassword('aypwtqkpykzalwsv');
                        $mailer = new Mailer($transport);

            $signatureComponents = $verifyEmailHelper->generateSignature(
                "app_verify_email",
                $user->getId(),
                $user->getEmail()
            );
            // generate a signed url and email it to the user
          $email = 
                (new TemplatedEmail())
                    ->from(new Address('eseht.xila@gmail.com', 'Alix These'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->html($this->renderView('registration/confirmation_email.html.twig', [
                        "signedUrl" => $signatureComponents->getSignedUrl(),
                        "expiresAtMessageKey" => $signatureComponents->getExpirationMessageKey(),
                        "expiresAtMessageData" => $signatureComponents->getExpirationMessageData(),

                    ]));

                    $mailer->send($email);
      
            //do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
