<?php

namespace WebStoreBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use WebStoreBundle\Entity\Item;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('price', MoneyType::class, array(
                'currency' => 'BGN',
            ))
            ->add('description', TextareaType::class)
            ->add('category', EntityType::class, array(
                'class' => 'WebStoreBundle\Entity\Category',
                'placeholder' => 'Choose a category'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Item::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'web_store_bundle_item_type';
    }
}
