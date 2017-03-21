<?php

namespace StcBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;

class InscriptionType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('login', FormType\TextType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Entrez votre pseudo',
                        'required' => 'true')))
                ->add('password', FormType\RepeatedType::class, array(
                    'type' => FormType\PasswordType::class,
                    'invalid_message' => 'Les mots de passe ne sont pas identiques',
                    'first_options' => array('label' => false,
                        'attr' => array(
                            'class' => 'form-control',
                            'placeholder' => 'Entrez un mot de passe',
                            'required' => 'true')),
                    'second_options' => array('label' => false,
                        'attr' => array('class' => 'form-control',
                            'placeholder' => 'Retapez votre mot de passe',
                            'required' => 'true'))))
                ->add('mail', FormType\EmailType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Entrez votre adresse email',
                        'required' => 'true')))
                ->add('submit', FormType\SubmitType::class, array(
                    'attr' => array('class' => 'form-control btn-primary',
                        'value' => 'S\'inscrire')))
        ;
    }

}
