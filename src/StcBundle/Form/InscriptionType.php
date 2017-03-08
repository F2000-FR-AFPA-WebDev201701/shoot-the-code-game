<?php

namespace StcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                ->add('password', FormType\PasswordType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Entrez un mot de passe',
                        'required' => 'true')))
                ->add('password2', FormType\PasswordType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Retapez votre mot de passe',
                        'required' => 'true')))
                ->add('mail_inscription', FormType\EmailType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Entrez votre adresse email',
                        'required' => 'true')))
                ->add('submit', FormType\SubmitType::class, array(
                    'attr' => array('class' => 'form-control btn-primary',
                        'value' => 'S\'inscrire')))
        ;
    }

}