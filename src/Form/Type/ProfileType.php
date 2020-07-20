<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, [
                'first_options' => ['label' => 'form.profile.password.first'],
                'second_options' => ['label' => 'form.profile.password.second'],
                'type' => PasswordType::class,
                'invalid_message' => 'form.error.password.repeated',
                'mapped' => true,
                'required' => !in_array('user_edit', $options['validation_groups']),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
