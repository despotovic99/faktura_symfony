<?php

namespace App\Form;

use App\Entity\StavkaFakture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StavkaFaktureType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('id', HiddenType::class)
            ->add('naziv_artikla', TextType::class, [
                'label'=>false,
                'attr' => ['placeholder'=>'Naziv artikla'],
            ])
            ->add('kolicina', NumberType::class, [
                'label'=>false,
                'attr' => ['placeholder'=>'Kolicina'],
            ])//            ->add('sacuvaj', ButtonType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => StavkaFakture::class,
        ]);
    }
}
