<?php

namespace App\Form;

use App\Entity\Parcour;
use App\Entity\TypeSortie;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityRepository;

class ParcourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('heureDebut', DateTimeType::class,[
               'date_widget'=>'single_text',
               'time_widget'=>'single_text',
            ])
            ->add('commentaire', TextAreaType::class,['attr'=> ['cols'=> 45, 'rows'=> 7 ]])
            
            ->add('TypeSortie', EntityType::class, [            
                'class' => TypeSortie::class,            
                'choice_label' => 'libelle',
                'choice_value' => 'id',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.libelle', 'ASC');},
                'multiple' => false,
                'expanded' => false,
                'mapped'=>true,
                'required'=>true,
                'attr' => 
                    [
                        'class' =>'form-select'
                    ]
                
            ])
            ->add('user', EntityType::class, [            
                'class' => User::class,            
                'choice_label' => 'username',
                'choice_value' => 'id',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC');},
                'multiple' => false,
                'expanded' => false,
                'mapped'=>true,
                'required'=>true,
                'attr' => 
                    [
                        'class' =>'form-select'
                    ]
                
            ])
            ->add('reset',ResetType::class,['attr' => ['class' =>'btn btn-primary']])
            ->add('Valider', SubmitType::class,['attr' => ['class' =>'btn btn-primary']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parcour::class,
        ]);
    }
}
