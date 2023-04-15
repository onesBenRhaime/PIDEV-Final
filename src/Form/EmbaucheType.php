<?php

namespace App\Form;

use App\Entity\Embauche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class EmbaucheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('poste')

            ->add('date_embauche', /*DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'Date',
                'required' => true,
                'format' => 'dd-MM-yyyy',
                'invalid_message' => 'La date saisie n\'est pas valide.',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Date([
                        'message' => 'La date saisie n\'est pas valide.',
                    ]),
                ],
                'mapped' => false,
                'data' => new \DateTime(),
                'min' => (new \DateTime())->format('Y-m-d') // This is the minimum date
            ]*/)
            ->add('salaire', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 4]),
                ],
            ])
            ->add('duree')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Embauche::class,
        ]);
    }
}
