<?php

namespace App\Form;

use Form\Model\ArticleFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme')
            ->add('title')
            ->add('keyword0')
            ->add('keyword1')
            ->add('keyword2')
            ->add('keyword3')
            ->add('keyword4')
            ->add('keyword5')
            ->add('keyword6')
            ->add('sizeMin')
            ->add('sizeMax')
            ->add('words0')
            ->add('countWords0')
            ->add('words1')
            ->add('countWords1')
            ->add('words2')
            ->add('countWords2')
            ->add('images', FileType::class, [
                'multiple' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleFormModel::class,
        ]);
    }
}
