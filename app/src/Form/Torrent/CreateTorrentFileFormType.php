<?php

namespace App\Form\Torrent;

use App\Entity\Category;
use App\Entity\TorrentFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CreateTorrentFileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('torrentFile', FileType::class, [
                'label' => 'Torrent',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
//                'constraints' => [
//                    new File([
//                        'mimeTypes' => [
//                            'application/x-bittorrent',
//                        ],
//                        'mimeTypesMessage' => 'Please upload a file with the extension .torrent',
//                    ])
//                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TorrentFile::class,
        ]);
    }
}
