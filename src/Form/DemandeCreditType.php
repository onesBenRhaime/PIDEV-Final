<?php

namespace App\Form;
use App\Entity\DemandeCredit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Component\Form\Extension\Core\Type\DateTimeImmutable;


class DemandeCreditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('creditId') 
        ->add('userId')
        ->add('amount')        
        // ->add('createdAt')  
        ->add('note',TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])               
        ->add('cin1', FileType::class, [               
                'mapped' => false, 
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Inserer une image validee',
                    ])
                ],             
        ])       
        ->add('cin2', FileType::class, [               
                'mapped' => false, 
                'required' => true,
                'constraints' => [
                    new File([
                         'maxSize' => '1024k',
                        'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                     ],
                    'mimeTypesMessage' => 'Inserer une image validee',
                    ])
                ],             
            ])
    //    ->add('save',SubmitType::class)
           
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeCredit::class,
        ]);
    }
}
