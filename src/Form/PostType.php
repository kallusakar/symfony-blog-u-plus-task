<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('text', TextareaType::class, ['attr' => ['class' => 'form-control']])
            ->add('isPublic', CheckboxType::class, ['required' => false, 'attr' => ['class' => 'form-check-input']])
            ->add('url', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('submit', SubmitType::class, ['label' => 'Submit', 'attr' => ['class' => 'btn btn-primary']]);

    }
}