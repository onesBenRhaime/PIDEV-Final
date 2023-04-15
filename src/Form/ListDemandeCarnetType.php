<?php

namespace App\Form;

use App\Entity\CarnetCheque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListDemandeCarnetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('identifier')
            ->add('description')
            ->add('cinS1')
            ->add('cinS2')
            ->add('document')
            ->add('idtypecarnet')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarnetCheque::class,
        ]);
    }
}
