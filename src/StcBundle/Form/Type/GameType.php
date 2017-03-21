<?php

namespace StcBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as FormType;

class GameType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('name', FormType\TextType::class, array(
                    'label' => 'Nom de la partie',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Nom de la partie',
                        'value' => 'Partie de ' . $options['nom']
                    )
                ))
                ->add('maxPlayers', FormType\IntegerType::class, array(
                    'label' => 'Nombre de joueurs',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Nombre de joueurs',
                        'value' => 1,
                        'min' => 1,
                        'max' => 4
                    )
                ))
                ->add('save', FormType\SubmitType::class, array(
                    'label' => 'Créer la partie',
                    'attr' => array(
                        'class' => 'btn btn-primary',
                        'value' => 'Valider'
                    )
                ))
                ->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'StcBundle\Entity\Game',
            'nom' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'stcbundle_game';
    }

}
