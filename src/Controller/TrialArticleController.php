<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ArticleService;

class TrialArticleController extends AbstractController
{
    /**
     * @Route("/trial", name="app_trial_article")
     * @param ArticleService $articleService
     * @param Request $request
     * @return Response
     */
    public function getTrialArticle(ArticleService $articleService, Request $request): Response
    {
        $trialArticleId = $request->cookies->get('trialArticleId');

        if ($request->query->get('trial_keyword') === "" || $request->query->get('trial_keyword') === null) {
            $keyword = null;
        } else {
            $keyword = [];
            array_push($keyword, $request->query->get('trial_keyword'));
        }

        $title = $request->query->get('trial_title');
        $article = $articleService->createArticle(null, null, $keyword,  $title, null, null, null, $trialArticleId);

        if ($request->query->get('trial_keyword') !== null && $request->query->get('trial_title') !== null) {
            $articleService->createArticle(null, null, $keyword,  $title, null, null, null, $trialArticleId, true);
        }

        if ($trialArticleId === null) {
            $response = new Response();
            $response->headers->setCookie(new Cookie('trialArticleId', sha1(uniqid('trialArticleId')), new \DateTimeImmutable('2023-12-12 23:59:59')));

            return $this->render('free_access/trial_article/index.html.twig', [
                'article' => $article->getBody(),
                'checkTrialArticleId' => $articleService->checkTrialArticleId($trialArticleId),
                ],
                $response,
            );
        }

        return $this->render('free_access/trial_article/index.html.twig', [
            'article' => $articleService->checkTrialArticleId($trialArticleId) ? $articleService->getTrialArticle($trialArticleId) : $article->getBody(),
            'checkTrialArticleId' => $articleService->checkTrialArticleId($trialArticleId),
        ]);
    }
}
