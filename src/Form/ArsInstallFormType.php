<?php

namespace App\Form;

use App\Entity\TicketReseau;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArsInstallFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('arsInstall', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'le numéro ARS est obligatoire pour l\'installation du kit-4G'
                    ])
                ],
                'attr' => [
                    'autofocus' => true,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TicketReseau::class,
        ]);
    }
}
