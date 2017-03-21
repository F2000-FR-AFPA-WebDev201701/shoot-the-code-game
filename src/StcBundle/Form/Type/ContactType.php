<?php

namespace StcBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;

class ContactType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', FormType\TextType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Entrez votre nom',
                        'required' => 'true')))
                ->add('email', FormType\TextType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Entrez votre email',
                        'required' => 'true')))
                ->add('message', FormType\TextareaType::class, array(
                    'attr' => array('class' => 'form-control',
                        'placeholder' => 'Entrez votre message',
                        'required' => 'true',
                        'rows' => 6)))
                ->add('submit', FormType\SubmitType::class, array(
                    'attr' => array('class' => 'form-control btn-primary',
                        'value' => 'Valider')))
        ;
    }

}
