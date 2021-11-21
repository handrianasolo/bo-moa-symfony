<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class IBMXLSXFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('file', FileType::class, [
          'label' => false,
          'mapped' => false,
          'required' => false,
          'constraints' => [
            new NotBlank(['message' => 'Selectionnez un fichier excel']),
            new File([
              'mimeTypes' => [
                'application/vnd.ms-excel', 
                'application/excel', 
                'application/vnd.msexcel', 
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
              ], 
              'mimeTypesMessage' => 'Le format du fichier n\'est pas valide.'
            ])
          ],
          'attr' => [
            'class' => 'p-1',
            'name' => 'excel_file',
            'id' => 'excel_file',
          ]
        ])
      ;
    }
}
