<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
//use Symfony\Component\Form\Extension\Core\Type\PasswordType;
//use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    private $user;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->user = $options['user'];

        $builder
            ->add('name', TextType::class, [
                'data' => $this->user->getName()
            ])
            ->add('lastname', TextType::class, [
                'data' => $this->user->getLastname()
            ])
            ->add('email', EmailType::class, [
                'data' => $this->user->getEmail()
            ])
//            ->add('currentPassword', PasswordType::class, [
//                'required' => false
//            ])
//            ->add('password', RepeatedType::class, [
//                'type' => PasswordType::class,
//                'required' => false,
//                'first_options' => ['label' => 'form.profile.password.first'],
//                'second_options' => ['label' => 'form.profile.password.second'],
//                'invalid_message' => 'form.error.password.repeated'
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null
        ]);
    }
}
