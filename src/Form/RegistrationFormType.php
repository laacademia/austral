<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,[
                'attr' => ['class' => 'form-control'] 
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => false,
                'attr' => ['class' => 'form-check-input'],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Debes aceptar los terminos.',
                    ]),
                ],
            ])
            ->add('plainPassword',RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'No coincide la contraseña con la confirmacion.',
                'options' => ['attr' => ['class' => 'password-field form-control']],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Contraseña',
                'mapped' => false,
                'first_options'  => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Confirmación'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese la contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} carácteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
