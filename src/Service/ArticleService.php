<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ArticleService
{
    public function __construct(
        ModuleService          $moduleService,
        ArticleContentProvider $articleContentProvider,
        EntityManagerInterface $em,
        ArticleRepository      $repository,
        SubscriptionService    $subscriptionService,
        ImagesService          $imagesService,
        FileUploader           $articleFileUploader
    ) {
        $this->moduleService = $moduleService;
        $this->articleContentProvider = $articleContentProvider;
        $this->em = $em;
        $this->repository = $repository;
        $this->subscriptionService = $subscriptionService;
        $this->imagesService = $imagesService;
        $this->articleFileUploader = $articleFileUploader;
    }

    /**
     * @param string|null $theme
     * @param array|null $size
     * @param array|null $keyword
     * @param string|null $title
     * @param array|null $words
     * @param int|null $id
     * @param User|null $user
     * @param string|null $trialArticleId
     * @param bool $save
     * @param array|null $filesNames
     * @return Article
     */
    public function createArticle(
        string $theme = null,
        array $size = null,
        array $keyword = null,
        string $title = null,
        array $words = null,
        int $id = null,
        User $user = null,
        string $trialArticleId = null,
        bool $save = false,
        array $filesNames = null
    ): Article {

        $themeChecked = $this->articleContentProvider->getTheme($theme);

        if ($id !== null && $this->getArticleById($id) !== null) {
            $article =  $this->getArticleById($id);
            $imagesId = $article->getImages() !== null ? $this->imagesService->getImagesById( $article->getImages()->getId() )->getId() : null;

            if (count($filesNames) > 0) {
                $this->articleFileUploader->deleteFilesArray($article->getImages() ? $article->getImages()->getFilesNames() : null);
                $images = $this->imagesService->setImages($filesNames, $imagesId ?? null);
            } else {
                $images = $article->getImages() ?? null;
            }

        } else {
            $article = new Article();

            if ($user !== null) {
                $article->setAuthor($user);
            }

            if ($trialArticleId !== null) {
                $article->setTrialArticleId($trialArticleId);
            }

            if ($filesNames === null || count($filesNames) === 0) {
                $images = null;
            }

            if ( is_array($filesNames) && count($filesNames) > 0) {
                $images = $this->imagesService->setImages($filesNames, $imagesId ?? null);
            }
        }

        $body = $this->moduleService->getLayout(
            $size ?: [7],
            $title ?: $this->articleContentProvider->getTitle($themeChecked, $keyword),
            $this->articleContentProvider->getSubtitle($themeChecked, $keyword),
            $this->articleContentProvider->getParagraph($themeChecked,$keyword, $words),
            $this->articleContentProvider->getManyParagraphs($themeChecked, $keyword, $words),
            $images
        );

        $article
            ->setBody($body)
            ->setTitle($title ?: $this->articleContentProvider->getTitle($themeChecked, $keyword))
            ->setTheme($this->articleContentProvider->getTheme($themeChecked))
            ->setKeyword($keyword ?: $this->articleContentProvider->getThemeObject($themeChecked)->getKeyword())
            ->setSize($size)
            ->setWords($words)
            ->setImages($images ?? null)
        ;

        if ($save) {
            $this->em->persist($article);
            $this->em->flush();
        }

        return $article;
    }

    /**
     * @param string $id
     * @return string
     */
    public function getTrialArticle(string $id): string
    {
        return $this->em->getRepository(Article::class)->findOneBy(['trialArticleId' => $id])->getBody();
    }

    /**
     * @param string|null $id
     * @return bool
     */
    public function checkTrialArticleId(?string $id): bool
    {
        if ($this->em->getRepository(Article::class)->findOneBy(['trialArticleId' => $id]) === null
            || $id === null) {
            return false;
        }

        return true;
    }

    /**
     * @param string|null $id
     * @param User $author
     * @return void
     */
    public function setAuthorForTrialArticle(?string $id, User $author): void
    {
        if (!$this->checkTrialArticleId($id)) {
            return;
        }

        /** @var Article $article */
        $article = $this->em->getRepository(Article::class)->findOneBy(['trialArticleId' => $id]);
        $article->setAuthor($author);

        $this->em->persist($article);
        $this->em->flush();
    }

    /**
     * @param int|null $id
     * @return Article|null
     */
    public function getArticleById(?int $id): ?Article
    {
        return $this->em->getRepository(Article::class)->findOneBy(['id' => $id]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getAllArticlesByAuthor(int $id): array
    {
        return $this->em->getRepository(Article::class)->findBy(['author' => $id],  ['id' => 'desc']);
    }

    /**
     * @param int $id
     * @return int
     */
    public function getArticlesLastMonthByAuthor(int $id): int
    {
        return $this->repository->findArticlesLastMonthByAuthor($id);
    }

    /**
     * @param int|null $id
     * @param FormInterface $form
     * @param User $user
     * @return Article|null
     */
    public function handlerFormRequest(
        ?int $id,
        FormInterface $form,
        User $user
    ): ?Article {

        if ($form->isSubmitted()) {

            /** @var UploadedFile[] $images */
            $images = $form->get('images')->getData();

            if ($images !== null) {
                $filesNames = [];

                $imagesLimit = $this->subscriptionService->checkLimitImages();

                if (count($images) > $imagesLimit) {
                    $images = array_slice($images, 0, $imagesLimit);
                }

                foreach ($images as $image) {
                    $filesNames[] = $this->articleFileUploader->uploadFile($image);
                }
            }

            $theme = $form->get('theme')->getData();

            $keyword = [
                $form->get('keyword0')->getData(),
                $form->get('keyword1')->getData(),
                $form->get('keyword2')->getData(),
                $form->get('keyword3')->getData(),
                $form->get('keyword4')->getData(),
                $form->get('keyword5')->getData(),
                $form->get('keyword6')->getData()
            ];

            $title = $form->get('title')->getData();

            $size = [$form->get('sizeMin')->getData(), $form->get('sizeMax')->getData()];

            $words = [
                [ 'word' => $form->get('words0')->getData(), 'count' => $form->get('countWords0')->getData()],
                [ 'word' => $form->get('words1')->getData(), 'count' => $form->get('countWords1')->getData()],
                [ 'word' => $form->get('words2')->getData(), 'count' => $form->get('countWords2')->getData()]
            ];

            return $this->createArticle(
                $theme,
                $size,
                $keyword,
                $title,
                $words,
                $id,
                $user,
                null,
                true,
                $filesNames
            );
        }

        return null;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function handlerFormRequestForApi(
        Request $request,
        User $user
    ): array {
        $arguments = $request->toArray();

        $theme = $arguments['theme'] ?? null;
        $size = $arguments['size'] ?? null;
        $keyword = $arguments['keyword'] ?? null;
        $title = $arguments['title'] ?? null;
        $words = $arguments['words'] ?? null;
        $images = $arguments['images'] ?? null;

        if ($images !== null) {
            $filesNames = [];

            $imagesLimit = $this->subscriptionService->checkLimitImages();

            if (count($images) > $imagesLimit) {
                $images = array_slice($images, 0, $imagesLimit);
            }

            foreach ($images as $image) {
                $filesNames[] = $this->articleFileUploader->downloadFile($image);
            }
        } else $filesNames = null;

        $article = $this->createArticle($theme, $size, $keyword, $title, $words, null, $user, null, true, $filesNames);

        return [
            'title' => $article->getTitle(),
            'description' => 'Статья о ' . $article->getKeyword()[0],
            'content' => $article->getBody(),
        ];

    }

    /**
     * @param Article|null $article
     * @param Security $security
     * @return bool
     */
    public function checkRightForArticle(?Article $article, Security $security): bool
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $articleAsArray = $serializer->normalize($article, null,  [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($user) {
                return $user->getId();
            },
        ]);

        if ( $article === null
            || !array_key_exists('author', $articleAsArray)
            || $security->getUser()->getId() !== $article->getAuthor()->getId()
        ) {
            return false;
        }
        return true;
    }
}