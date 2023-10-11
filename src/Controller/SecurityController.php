<?php

namespace App\Controller;

use App\Service\UserService;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Service\SubscriptionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserService $userService, SubscriptionService $subscriptionService): Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);

        /** @var UserRegistrationFormModel $userModel */
        $userModel = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userService->createUser(
                $userModel->email,
                $userModel->firstName,
                $userModel->plainPassword,
            );
            $subscriptionService->createUserDefaultSubscription($user);

            $this->addFlash(
                'registration_success',
                'Для завершения регистрации подтвердите ваш email, 
                перейдя по ссылке в отправленном вам письме'
            );

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @param bool|null $emailIsVerified
     * @return Response|null
     */
    public function getResultVerifyEmail(?bool $emailIsVerified): ?Response
    {
        if ($emailIsVerified === null) {
            $this->addFlash('error_email_verified', 'Такой пользователь не найден, попробуйте ещё раз');
            return $this->redirectToRoute('app_register');
        }

        if (!$emailIsVerified) {
            $this->addFlash('error_email_verified', 'Ссылка для подтверждения email повреждена или устарела, попробуйте ещё раз');
            return $this->redirectToRoute('app_register');
        }

        return null;
    }

    /**
     * @Route("/verify-registration-mail", name="app_verify_registration_mail")
     */
    public function loginAfterVerifyEmail(Request $request, UserService $userService): Response
    {
        $emailIsVerified = $userService->verifyEmail(
            $request->query->get('id'),
            $request->getUri(),
            function () use ($userService, $request) {
                $userService->setEmailIsVerified($request->query->get('id'));
            }
        );

        $this->getResultVerifyEmail($emailIsVerified);

        $this->addFlash('success_email_verified', 'Ваш email подтверждён, вы можете войти на сайт');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/verify-updating-mail", name="app_verify_updating_mail")
     */
    public function loginAfterVerifyUpdatingEmail(Request $request, UserService $userService): Response
    {
        $emailIsVerified = $userService->verifyEmail(
            $request->query->get('id'),
            $request->getUri(),
            function () use ($userService, $request) {
                $userService->setUpdatedEmail($request->query->get('id'));
            }
        );

        $this->getResultVerifyEmail($emailIsVerified);

        return $this->redirectToRoute('app_logout');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException();
    }
}
