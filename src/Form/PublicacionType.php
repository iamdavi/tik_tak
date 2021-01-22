<?php

namespace App\Form;

use App\Entity\Publicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PublicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $button_label = $builder->getData()->getId() ? 'Actualizar' : 'Guardar';
        $builder
            ->add('videoFile', VichFileType::class, [
                'label'             => 'Vídeo del TikTak',
                'required'          => false,
                'allow_delete'      => true,
                'download_uri'      => false,
                'asset_helper'      => true,
                'download_label'    => static function (Publicacion $publicacion) {
                    return $publicacion->getTitulo();
                },
            ])
            ->add('titulo', TextType::class)
            ->add('description', TextareaType::class)
            ->add('actualizar', SubmitType::class, [
                'attr'  => ['class' => 'btn btn-dark float-right'],
                'label' => $button_label
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Publicacion::class,
        ]);
    }
}
