<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('montant')
            ->add('requestFrom')
            ->add('requestTo')
            ->add('agenceName', ChoiceType::class, [
                'choices' => $options['choices'],
                'placeholder' => 'Choose an agence',
            ])
            ->add('compte') 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'choices' => ['no agence'],
        ]);
    }
}
