<?php

namespace App\Form;

use App\Entity\UserV;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('name')
            ->add('dateCreation', DateType::class,['attr' => ['class' => 'association_fields']])
            ->add('numTel')
            ->add('description', TextType::class,['attr' => ['class' => 'association_fields']])
            ->add('image',FileType::class, [
                'label' => 'add image',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1G',
                        'mimeTypes'=> [
                            'image/jpeg',
                            'image/png' ,
                            'image/gif' ,
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)',
                    ])
                ]], ['attr' => ['class' => 'association_fields']])
            ->add('local',TextType::class, ['attr' => ['class' => 'association_fields']])
            ->add('ceo',TextType::class, ['attr' => ['class' => 'association_fields']])
            ->add('lastName')
            ->add('skills', TextType::class,['attr' => ['class' => 'volontaire_fields']])
            ->add('availability',TextType::class, ['attr' => ['class' => 'volontaire_fields']])
            ->add('roles', ChoiceType::class, ["choices"=>['VOLONTAIRE'=>'ROLE_VOLONTAIRE','ASSOCIATION'=>'ROLE_ASSOCIATION']])
        ;
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function($tagAsArray):string{
                    return implode(',',$tagAsArray);
                },
                function ($tagAsString):array{
                    return explode(',',$tagAsString);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserV::class,
        ]);
    }
}
