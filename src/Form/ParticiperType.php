<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Participer;
use App\Entity\UserV;
use App\Entity\Volontaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticiperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat')
            ->add('volontaire', EntityType::class, [
                'class' => UserV::class,
                'choice_label' => 'name',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('u')
                        ->where('u.roles like :role')
                        ->setParameter('role', '%ROLE_VOLONTAIRE%');
                },
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participer::class,
        ]);
    }
}
