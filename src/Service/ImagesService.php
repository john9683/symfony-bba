<?php

namespace App\Service;

use App\Entity\Images;
use Doctrine\ORM\EntityManagerInterface;

class ImagesService
{
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @param array $filesNames
     * @param int|null $id
     * @return void
     */
    public function setImages(array $filesNames, int $id = null): Images
    {
       if ($id !== null && $this->getImagesById($id) !== null) {
           $images =  $this->getImagesById($id);
       } else {
           $images = new Images();
       }

        $images->setFilesNames($filesNames);
        $this->em->persist($images);
        $this->em->flush();

        return $images;
    }

    /**
     * @param int|null $id
     * @return Images|null
     */
    public function getImagesById(?int $id): ?Images
    {
        return $this->em->getRepository(Images::class)->findOneBy(['id' => $id]);
    }
}