<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Service\ArticleService;
use App\Service\FileUploader;
use App\Service\SubscriptionService;
use App\Service\ThemeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ArticlesController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/dashboard/articles", name="app_dashboard_articles")
     */
    public function index(
        Request $request,
        ArticleService $articleService,
        PaginatorInterface $paginator
    ): Response {

        /** @var User $user */
        $user = $this->getUser();

        $pagination = $paginator->paginate(
            $articleService->getAllArticlesByAuthor($user->getId()),
            $request->query->getInt('page', 1),
            !$request->query->get('limitPerPage') ? 10 : $request->query->get('limitPerPage')
        );

        return $this->render('dashboard/articles/index.html.twig', [
            'setHtmlClassActive' => 'articles',
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/dashboard/create", name="app_dashboard_create")
     */
    public function create(
        Request $request,
        ArticleService $articleService,
        SubscriptionService $subscriptionService,
        ThemeService $themeService
    ): Response {

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ArticleFormType::class);
        $form->handleRequest($request);

        $article = $articleService->handlerFormRequest(null, $form, $user);

        if ($article) {
            $this->addFlash(
                'article_create',
                'Статья "' . $article->getTitle() . '" создана, её можно прочитать под формой создания'
            );
        }

        return $this->render('dashboard/articles/create.html.twig', [
            'limitArticlesPerHour' => $subscriptionService->checkLimitForCreateArticle(),
            'setHtmlClassActive' => 'create',
            'createForm' => $form->createView(),
            'themesArray' => $themeService->getThemesArray(),
            'articleLayout' => $article ? $article->getBody() : null,
            'buttonText' => $article ? 'Создать новую статью' : 'Создать',
            'formTitle' => 'Создание статьи',
            'articleObject' => $article,
            'articleExists' => (bool)$article,
            'imagesExists' => false,
            'imagesArray'=> $article && $article->getImages() ? $article->getImages()->getFilesNames() : null,
            'imagesLimit' => $subscriptionService->checkLimitImages(),
        ]);
    }

     /**
     * @Route("/dashboard/articles/{id}", name="app_dashboard_show")
     */
    public function show(int $id, ArticleService $articleService, Security $security): Response
    {
        /** @var Article|null $article */
        $article = $articleService->getArticleById($id);

        if (!$articleService->checkRightForArticle($article, $security)) {
            return  $this->render(('dashboard/dashboard404.html.twig'));
        };

        return $this->render('dashboard/articles/show.html.twig', [
            'setHtmlClassActive' => 'articles',
            'articleBody'=> $article->getBody(),
            'id' => $article->getId(),
        ]);
    }

    /**
     * @Route("/dashboard/articles/{id}/edit", name="app_dashboard_edit")
     */
    public function edit(
        int $id,
        Request $request,
        ArticleService $articleService,
        ThemeService $themeService,
        SubscriptionService $subscriptionService,
        Security $security
    ): Response {

        /** @var Article|null $article */
        $article = $articleService->getArticleById($id);

        /** @var User $user */
        $user = $this->getUser();

        if (!$articleService->checkRightForArticle($article, $security)) {
            return  $this->render(('dashboard/dashboard404.html.twig'));
        };

        $form = $this->createForm(ArticleFormType::class);
        $form->handleRequest($request);

        $newArticle = $articleService->handlerFormRequest($id, $form, $user);

        if ($newArticle) {
            $this->addFlash(
                'article_edit',
                'Статья "' . $article->getTitle() . '" изменена, её можно прочитать под формой редактирования'
            );
        }

        return $this->render('dashboard/articles/create.html.twig', [
            'setHtmlClassActive' => 'create',
            'limitArticlesPerHour' => true,
            'createForm' => $form->createView(),
            'themesArray' => $themeService->getThemesArray(),
            'articleLayout' => $newArticle ? $newArticle->getBody() : null,
            'articleObject'=> $articleService->getArticleById($id),
            'buttonText' => $newArticle ? 'Редактировать статью ещё раз': 'Редактировать статью',
            'formTitle' => 'Редактировать статью',
            'articleExists' => (bool)$newArticle,
            'imagesExists' => (bool)$article->getImages(),
            'imagesArray'=> $article->getImages() ? $article->getImages()->getFilesNames() : null,
            'imagesLimit' => $subscriptionService->checkLimitImages(),
        ]);
    }
}




