<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\ArticleService;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/api/article_content", methods={"POST"}, name="app_api_article_content")
     * @param ArticleService $articleService
     * @param SubscriptionService $subscriptionService
     * @param Request $request
     * @return JsonResponse
     */
    public function getArticleContent(
        ArticleService $articleService,
        SubscriptionService $subscriptionService,
         Request $request
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($subscriptionService->checkLimitForCreateArticle() === false) {
            return $this->json([
                'Message' => 'Превышен лимит создания статей, чтобы снять лимит - улучшите подписку',
            ]);
        }

        if ($subscriptionService->checkLimitImages() === 0) {
            return $this->json([
                'Message' => 'Ваша подписка не позваоляет загружать изображения, чтобы снять лимит - улучшите подписку',
            ]);
        }

        /** @var User $user */
        $user = $this->getUser();

        $response = $articleService->handlerFormRequestForApi($request, $user);

        return $this->json([
            'title' => $response['title'],
            'description' => $response['description'],
            'content' => $response['content'],
        ]);
    }
}