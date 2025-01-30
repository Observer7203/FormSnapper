<?php

// src/Form/RegistrationType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter email'],
                'constraints' => [
                    new Assert\NotBlank(message: "Email must not be empty"),
                    new Assert\Email(message: "Enter correct Email"),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // Чтобы Symfony не пытался записать в БД
                'attr' => ['class' => 'form-control', 'placeholder' => 'Введите пароль'],
                'constraints' => [
                    new Assert\NotBlank(message: "The password must not be empty"),
                    new Assert\Length(
                        min: 6,
                        minMessage: "Пароль должен содержать не менее {{ limit }} символов."
                    ),
                ],
                'first_options' => [
                    'label' => 'Password',
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Введите пароль']
                ],
                'second_options' => [
                    'label' => 'Repeat password',
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Повторите пароль']
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
