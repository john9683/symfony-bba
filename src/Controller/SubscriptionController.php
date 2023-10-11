<?php

namespace App\Controller;

use App\Form\UserSubscriptionFormType;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * @Route("/dashboard/subscription", name="app_dashboard_subscription")
     */
    public function buySubscription(
        SubscriptionService $subscriptionService,
        Request $request
    ): Response {

        $formUserSubscription = $this->createForm(UserSubscriptionFormType::class);
        $formUserSubscription->handleRequest($request);

        $subscriptionService->handlerFormRequest($formUserSubscription);

        return $this->render('dashboard/subscription/index.html.twig', [
            'setHtmlClassActive' => 'subscription',
            'subscriptionsArray' => $subscriptionService->getSubscriptionsTypesArray(),
            'userSubscription' => $subscriptionService->getCurrentUserSubscription(),
            'dateEndSubscription' => $subscriptionService->getDateEnd(),
            'userSubscriptionForm' => $formUserSubscription->createView(),
        ]);
    }
}
