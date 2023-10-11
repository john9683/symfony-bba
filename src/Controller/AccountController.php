<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ArticleService;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/dashboard/account", name="app_dashboard_account")
     */
    public function index(
        ArticleService $articleService,
        SubscriptionService $subscriptionService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('dashboard/account/index.html.twig', [
            'setHtmlClassActive' => 'account',
            'highestLevelSubscription' => $subscriptionService->getHighestLevelSubscription(),
            'allArticlesCount' => count($articleService->getAllArticlesByAuthor($user->getId())) ?? null,
            'articlesLastMonth' => $articleService->getArticlesLastMonthByAuthor($user->getId()) ?? null,
            'lastArticleObject' => $articleService->getAllArticlesByAuthor($user->getId())[0] ?? null,
            'userSubscription' => $subscriptionService->getCurrentUserSubscription(),
            'getSubscriptionWarning' => $subscriptionService->getWarning()[0],
            'dayCount' => $subscriptionService->getWarning()[1],
        ]);
    }
}
