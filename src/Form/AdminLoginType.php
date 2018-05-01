<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('password', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('login', SubmitType::class, ['label' => 'Login', 'attr' => ['class' => 'btn btn-primary offset-10']]);
    }
}