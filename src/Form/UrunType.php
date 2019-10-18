<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Brand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UrunType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, array('label' => 'Adı', 'attr' => array('class' => 'form-control') ))
            ->add('price', NumberType::class, array('label' => 'Fiyatı', 'attr' => array('class' => 'form-control') ))
            ->add('quantity', NumberType::class, array('label' => 'Stok Adeti', 'attr' => array('class' => 'form-control') ))
            ->add('discount', NumberType::class, array('label' => 'İndirim Oranı','data' => 0, 'attr' => array('class' => 'form-control') ))
            ->add('category', EntityType::class, array(
                'label' => 'Kategorisi',
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'custom-select form-control')
            ))
            ->add('brand', EntityType::class, array(
                'label' => 'Markası',
                'class' => Brand::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'custom-select form-control')
            ))
            ->add('description', TextareaType::class, array('label' => 'Açıklama', 'attr' => array('class' => 'form-control') ))
            ->add('image',FileType::class, array('mapped' => false,'required' => false,'label' => 'Resim', 'attr' => array('class' => 'form-control') ))
            ->add('Kaydet',SubmitType::class, array('label' => 'Kaydet', 'attr' => array('class' => 'btn btn-outline-success mt-3') ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
