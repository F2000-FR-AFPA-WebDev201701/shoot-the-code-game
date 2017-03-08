<?php

namespace StcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType {

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
                        'placeholder' => 'Entrez votre mot de passe',
                        'required' => 'true')))
                ->add('submit', FormType\SubmitType::class, array(
                    'attr' => array('class' => 'form-control btn-primary',
                        'value' => 'Se connecter')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'StcBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'stcbundle_user';
    }

}
