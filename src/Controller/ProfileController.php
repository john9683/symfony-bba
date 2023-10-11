<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ApiTokenFormType;
use App\Form\UserUpdateFormType;
use App\Service\UserService;
use Form\Model\ApiTokenFormModel;
use Form\Model\UserUpdateFormModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @param UserService $userService
     * @param User $user
     * @param FormInterface $formApiToken
     * @param FormInterface $formUpdateUser
     * @return Response
     */
    public function responseHandler(
        UserService $userService,
        User $user,
        FormInterface $formApiToken,
        FormInterface $formUpdateUser
    ): Response {

        return $this->render('dashboard/profile/index.html.twig', [
            'setHtmlClassActive' => 'profile',
            'apiTokenForm' => $formApiToken->createView(),
            'updateUserForm' => $formUpdateUser->createView(),
            'apiToken' => $userService->getUserById($user->getId())->getApiToken(),
            'firstName' => $userService->getUserById($user->getId())->getFirstName(),
            'email' => $userService->getUserById($user->getId())->getEmail(),
        ]);
    }

    /**
     * @Route("/dashboard/profile", name="app_dashboard_profile")
     */
    public function editProfile(UserService $userService, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $formApiToken = $this->createForm(ApiTokenFormType::class);
        $formApiToken->handleRequest($request);

        /** @var ApiTokenFormModel $apiTokenModel */
        $apiTokenModel = $formApiToken->getData();

        $formUpdateUser = $this->createForm(UserUpdateFormType::class);
        $formUpdateUser->handleRequest($request);

        /** @var UserUpdateFormModel $userModel */
        $userModel = $formUpdateUser->getData();

        if ($userService->handlerApiTokenFormRequest($formApiToken, $apiTokenModel)) {

            $this->addFlash(
                'update_apitoken',
                'Ваш API токен изменён'
        );

            return $this->responseHandler($userService, $user, $formApiToken, $formUpdateUser);
        }

        $flashArray = $userService->handlerProfileFormRequest($formUpdateUser, $userModel);

        if ($flashArray !== null) {
            foreach ($flashArray as $flash) {
                $this->addFlash(
                    'update_user',
                    $flash
                );
            }

            return $this->responseHandler($userService, $user, $formApiToken, $formUpdateUser);
        }

        return $this->responseHandler($userService, $user, $formApiToken, $formUpdateUser);
    }
}
