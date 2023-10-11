<?php

namespace App\Service;

use App\Entity\Theme;
use App\Entity\Paragraph;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class ArticleContentProvider
{
    public function __construct(
        Environment $environment,
        EntityManagerInterface $em,
        SubscriptionService $subscriptionService
    ) {
        $this->environment = $environment;
        $this->em = $em;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param string|null $theme
     * @return bool
     */
    public function checkTheme(?string $theme): bool
    {
        if ($theme === null || !$this->em->getRepository(Theme::class)->findOneBy(['code' => $theme])) {
            return false;
        }
        return true;
    }

    /**
     * @return Theme
     */
    public function getThemeDefault(): Theme
    {
        return $this->em->getRepository(Theme::class)->findOneBy(['isDefault' => true]);
    }

    /**
     * @param string|null $theme
     * @return Theme
     */
     public function getThemeObject(string $theme): Theme
    {
        $repository = $this->em->getRepository(Theme::class);
        $themeObject = $repository->findOneBy(['code' => $theme]);

        return $themeObject;
    }

    /**
     * @param string|null $theme
     * @return string
     */
    public function getTheme(?string $theme): string
    {
        if (!$this->checkTheme($theme)) {
            $themeObject = $this->getThemeDefault();
        } else {
            $themeObject = $this->getThemeObject($theme);
        }

        return $themeObject->getCode();
    }

    /**
     * @param string|null $theme
     * @param array|null $keyword
     * @return string
     */
    public function getTitle(?string $theme, ?array $keyword): string
    {
        if (!$this->checkTheme($theme)) {
            $themeObject = $this->getThemeDefault();
        } else {
            $themeObject = $this->getThemeObject($theme);
        }

        $template = $this->environment->createTemplate($themeObject->getTitle());

        return $template->render([
            'keyword' => $keyword ?: $themeObject->getKeyword(),
        ]);
    }

    /**
     * @param string|null $theme
     * @param array|null $keyword
     * @return string
     */
    public function getSubtitle(?string $theme, ?array $keyword): string
    {
        if (!$this->checkTheme($theme)) {
            $themeObject = $this->getThemeDefault();
        } else {
            $themeObject = $this->getThemeObject($theme);
        }

        $template = $this->environment->createTemplate($themeObject->getSubtitle());

        return $template->render([
            'keyword' => $keyword ?: $themeObject->getKeyword()
        ]);
    }

    /**
     * @param string $paragraphs
     * @param array $word
     * @return string
     */
    public function pasteWord(string &$paragraphs, array $word): string
    {
        $count = array_key_exists('count', $word) && $word['count'] !== null ? $word['count'] : 1;

        for ($i = 0; $i < $count; $i++) {
            $paragraphs = explode(' ', $paragraphs);
            array_splice($paragraphs, rand(2, count($paragraphs) - 1), 0, $word['word']);
            $paragraphs = implode(' ', $paragraphs);
        }

        return $paragraphs;
    }

    /**
     * @param string $paragraphs
     * @param array|null $words
     * @return string|null
     */
    public function pasteWordsArray(string $paragraphs, ?array $words): string
    {
         if ($words !== null) {
             $words = array_slice($words, 0, $this->subscriptionService->checkQuantityWords());
             foreach ($words as $word) {
                $this->pasteWord($paragraphs, $word);
             }
        }

        return $paragraphs;
    }

    /**
     * @param string|null $theme
     * @param array|null $keyword
     * @param array|null $words
     * @return string
     */
    public function getParagraph(?string $theme, ?array $keyword, ?array $words): string
    {
        if (!$this->checkTheme($theme)) {
            $themeObject = $this->getThemeDefault();
        } else {
            $themeObject = $this->getThemeObject($theme);
        }

        $paragraphs = $this->em->getRepository(Paragraph::class)->findBy(['theme' => $themeObject->getId()]);

        $paragraphsString = '';
        $paragraphsString .= $paragraphs[0]->getText();

        $content = $this->pasteWordsArray($paragraphsString, $words);
        $template = $this->environment->createTemplate($content);

        return $template->render([
            'keyword' => $keyword ?: $themeObject->getKeyword()
        ]);
    }

    /**
     * @param string|null $theme
     * @param array|null $keyword
     * @param array|null $words
     * @return string
     */
    public function getManyParagraphs(?string $theme, ?array $keyword, ?array $words): string
    {
        if (!$this->checkTheme($theme)) {
            $themeObject = $this->getThemeDefault();
        } else {
            $themeObject = $this->getThemeObject($theme);
        }

        $paragraphs = $this->em->getRepository(Paragraph::class)->findBy(['theme' => $themeObject->getId()]);

        $paragraphsString = '';
        for( $i = 0; $i < rand(1, 3); $i++){
            $paragraphsString .= $paragraphs[ rand(1, count($paragraphs)) - 1 ]->getText();
        }

        $content = $this->pasteWordsArray($paragraphsString, $words);
        $template = $this->environment->createTemplate($content);

        return $template->render([
            'keyword' => $keyword ?: $themeObject->getKeyword()
        ]);
    }

}
