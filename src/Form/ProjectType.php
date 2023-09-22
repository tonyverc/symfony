<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('clientName')
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                
            ])
            ->add('checkpointDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('deliveryDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('students', EntityType::class ,[
                'class' => Student::class,
                'choice_label' => function (Student $student){
                            
                    return "{$student->getFirstName()} (id {$student->getLastName()}";
                },
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.firstName', 'ASC')
                        ->addOrderBy('s.lastName', 'ASC');
                },
                //@warning
                // a ne rajouter que pour les associations qui sont le côté possedant
                // autrement dit ,nécessaire si dans l'entité Project, la propriété $student posséde l'attribut
                //mappedBy dans ce cas, Student est possédant et Project est possédé(aussi appelé côté inverse)
                'by_reference' => true,
            ])

            ->add('tags', EntityType::class ,[
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                // 'by_reference' => true,
                //@info
            ])  // pas nécessaire de rajoutter by_reference car l'association n'est pas le côté possedant
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
