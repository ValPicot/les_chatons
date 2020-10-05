<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('darkMode', CheckboxType::class)
            ->add('plainPassword', RepeatedType::class, [
                'first_options' => ['label' => 'form.profile.password.first'],
                'second_options' => ['label' => 'form.profile.password.second'],
                'type' => PasswordType::class,
                'invalid_message' => 'form.error.password.repeated',
                'mapped' => true,
                'required' => !in_array('user_edit', $options['validation_groups']),
            ])
        ;

        if (in_array('admin_user_create', $options['validation_groups'])) {
            $builder
                ->add('roles', CollectionType::class, [
                    'entry_type' => ChoiceType::class,
                    'entry_options' => [
                        'label' => false,
                        'choices' => [
                            'Admin' => 'ROLE_ADMIN',
                            'Propriétaire de chat' => 'ROLE_OWNER_CAT',
                        ],
                    ],
                ])
                ->add('isActive', CheckboxType::class)
                ->add('createdAt', HiddenType::class)
                ->add('updatedAt', HiddenType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
