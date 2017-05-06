<?php

namespace WebStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use WebStoreBundle\Entity\Comment;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, array(
                'label' => 'Comment (max 250 characters):'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Comment::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'web_store_bundle_comment_type';
    }
}
