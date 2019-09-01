<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, UserRepository $users)
    {
        $this->em = $em;
        $this->users = $users;
    }

    /**
     * @Route("/security/", name="security")
     */
    public function index()
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/security/signup", name="security_signup")
     */
    public function signup(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        if ($this->getUser() != null) {
            return $this->redirectToRoute('index');
        }

        $tmpUser = new User();

        $form = $this->createFormBuilder($tmpUser)
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'attr' => [
                    'placeholder' => 'hallo@jugendhackt.de'
                ]])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Passwort'
                ],
                'second_options' => [
                    'label' => 'Passwort wiederholen',
                ],
                ])
            ->add('register', SubmitType::class, [
                'label' => 'Registrieren',
                'attr' => [
                    'class' => 'btn-success btn-block'
                    ]])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $formData = $form->getNormData();
            $user = new User();

            if ($this->users->findOneBy(['email' => $formData->getEmail()]) !== null)
            {
                $form = $form->createView();
                $this->addFlash('danger', 'Diese E-Mail Adresse wird bereits verwendet');
                return $this->render('security/login.html.twig', compact('form'));
            }

            $user->setEmail($formData->getEmail());
            $user->setPassword($passwordEncoder->encodePassword($user, $formData->getPassword()));
            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('security_login');

            $this->addFlash('danger', $this->trans->trans('security.registerToken.invalid'));
        }
        $form = $form->createView();
        return $this->render('security/login.html.twig', compact('form'));
    }

    /**
     * @Route("/security/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils) {
        if ($this->getUser() != null) {
            return $this->redirectToRoute('index');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createFormBuilder(['remember_me' => true])
            ->add('email', EmailType::class, ['label' => 'E-Mail', 'attr' => [
                'value' => (isset($lastUsername) ? $lastUsername : ""),
                'placeholder' => 'hallo@jugendhackt.de'
                ]])
            ->add('password', PasswordType::class, [
                'label' => 'Passwort',
                'attr' => [
                    'placeholder' => '***********',
                ],
                ])
            ->add('login', SubmitType::class, [
                'label' => 'Anmelden',
                'attr' => [
                    'class' => 'btn-success btn-block'
                    ]])
            ->getForm()
            ->createView();
        return $this->render('security/login.html.twig',
        [
            'form' => $form,
            'error' => $error]);
    }

    /**
     * @Route("/security/logout", name="security_logout")
     */
    public function logout()
    {

    }
}
