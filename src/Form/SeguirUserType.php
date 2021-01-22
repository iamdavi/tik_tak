<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SeguirUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $checkbox_value = $options['siguiendo'] ? '0' : '1';
        $button_label   = $options['siguiendo'] ? 'Dejar de seguir' : 'Seguir usuario';
        $checked_value  = $options['siguiendo'] ? false : true;
        $builder
            ->add('seguirUsuario', CheckboxType::class, [
                'label'     => false,
                'required'  => false,
                'mapped'    => false,
                'attr'      => [
                    'checked' => $checked_value, 
                    'value' => $checkbox_value,
                    'class' => 'd-none'
                ]
            ])
            ->add('seguir', SubmitType::class, [
                'label' => $button_label,
                'attr' => ['class' => 'btn btn-dark']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => User::class,
            'siguiendo'     => false
        ]);
    }
}
