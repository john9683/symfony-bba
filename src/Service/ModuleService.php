<?php

namespace App\Service;

use App\Entity\Images;
use App\Entity\Module;
use App\Entity\User;
use Form\Model\ModuleFormModel;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;

class ModuleService
{
    public function __construct(
        Environment $environment,
        EntityManagerInterface $em,
        Security $security,
        SubscriptionService $subscriptionService,
        ImagesService $imagesService
    ) {
        $this->environment = $environment;
        $this->em = $em;
        $this->security = $security;
        $this->subscriptionService = $subscriptionService;
        $this->imagesService = $imagesService;
    }

    /**
     * @var int
     */
    private int $size;

    /**
     * @param array $size
     * @return int
     */
    public function setSize(array $size): int
    {
        $minSize = $size[0] > 2 ? $size[0] : 2;
        $maxSize = array_key_exists(1, $size) ? $size[1] : $size[0];
        $maxSize = $maxSize >= $minSize ? $maxSize : $minSize;

        $this->size = rand($minSize, $maxSize);

        return $this->size;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param array $size
     * @param string $title
     * @param string $subtitle
     * @param string $paragraph
     * @param string $paragraphs
     * @return string
     */
    public function getLayout(
        array $size,
        string $title,
        string $subtitle,
        string $paragraph,
        string $paragraphs,
        Images $images = null
    ): string {
        $this->setSize($size);

        $modulesArray =  $this->em->getRepository(Module::class)->findBy(
            ['isPublic' => true],
            ['id' => 'ASC']
        );

        if ($this->security->getUser() !== null) {
            $ownerModulesArray =  $this->em->getRepository(Module::class)->findBy(
                ['owner' => $this->security->getUser()->getId(), 'active' => true],
                ['id' => 'ASC']
            );
        } else $ownerModulesArray = [];


        if (count($ownerModulesArray) > 0 ) {
            $modulesArray = array_merge($modulesArray, $ownerModulesArray);
        }

        $modulesString = '';
        $modulesString .= $modulesArray[0]->getLayout();
        $modulesString .= $modulesArray[1]->getLayout();

        for( $i = 0; $i < $this->getSize() - 2; $i++){
            $modulesString .= $modulesArray[rand(2, count($modulesArray) - 1)]->getLayout();
        }

        $template = $this->environment->createTemplate($modulesString);

        return $template->render([
            'title' => $title,
            'subtitle' => $subtitle,
            'paragraph' => $paragraph,
            'paragraphs' => $paragraphs,
            'images' => $images
        ]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getAllModulesByOwner(int $id): array
    {
        return $this->em->getRepository(Module::class)->findBy(
            ['owner' => $id, 'active' => true],
            ['id' => 'desc']
        );
    }

    /**
     * @param int $id
     * @return array
     */
    public function setCountdownModules(int $id): array
    {
        $modulesArray = $this->getAllModulesByOwner($id);
        $count = count($modulesArray);

        foreach ($modulesArray as $module) {
            /** @var Module $module */
            $module->setNumber($count--);
        }
        return $modulesArray;
    }

    /**
     * @param int|null $id
     * @return Module|null
     */
    public function getModuleById(?int $id): ?Module
    {
        return $this->em->getRepository(Module::class)->findOneBy(['id' => $id]);
    }

    /**
     * @param Module|null $module
     * @param Security $security
     * @return bool
     */
    public function checkRightForModule(?Module $module, Security $security): bool
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $moduleAsArray = $serializer->normalize($module, null,  [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($user) {
                return $user->getId();
            },
        ]);

        if ( $module === null
            || !array_key_exists('owner', $moduleAsArray)
            || $security->getUser()->getId() !== $module->getOwner()->getId()
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param int $id
     * @return void
     */
    public function desactivateModule(int $id): void
    {
        $module = $this->getModuleById($id)->setActive(false);
        $this->em->persist($module);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @param string $title
     * @param string $layout
     * @return Module
     */
    public function createModule(User $user, string $title, string $layout): Module
    {
        $module = new Module();
        $module
            ->setOwner($user)
            ->setTitle($title)
            ->setLayout($layout)
            ->setActive(true)
            ->setIsPublic(false)
        ;

        $this->em->persist($module);
        $this->em->flush();

        return $module;
    }

    /**
     * @param FormInterface|null $form
     * @param ModuleFormModel|null $model
     * @return Module|null
     */
    public function handlerModuleFormRequest(?FormInterface $form, ?ModuleFormModel $model): ?Module
    {
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->security->getUser();

            return $this->createModule(
                $user,
                $model->title,
                $model->layout
            );
        }
        return null;
    }

    /**
     * @return bool
     */
    public function checkAccessToModulePage(): bool
    {
        return $this->subscriptionService->checkAccessToModulePage();
    }
}