<?php

namespace WebStoreBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichFileType;
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
            ))
            ->add('quantity', NumberType::class)
            ->add('discounted', CheckboxType::class, array(
                'required' => false,
                'label' => 'Put the item on discount?'))
            ->add('discount', NumberType::class)
            ->add('discountExpirationDate', DateType::class, ['years' => range(2017, 2020)])
            ->add('imageFile', VichFileType::class, array(
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
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
