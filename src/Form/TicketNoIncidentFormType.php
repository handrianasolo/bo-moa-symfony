<?php

namespace App\Form;

use App\Entity\TicketNoIncident;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketNoIncidentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomMagasin', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom du magasin est obligatoire'
                    ])
                ]
            ])
            ->add('codeMagasin', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le code du magasin est obligatoire'
                    ])
                ]
            ])
            ->add('ville', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom de la ville est obligatoire'
                    ])
                ]
            ])
            ->add('codeGeode', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le code géode est obligatoire'
                    ])
                ]
            ])
            ->add('nKit', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro du Kit-4G est obligatoire'
                    ])
                ]
            ])
            ->add('dateInstall', DateType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date d\'installation est obligatoire'
                    ])
                ],
                "widget" => 'single_text',
                "html5" => false,
                "format" => "dd/MM/yyyy",
                "empty_data" => "",
            ])
            ->add('commentaire', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 4,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TicketNoIncident::class,
        ]);
    }
}
 