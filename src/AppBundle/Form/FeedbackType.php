<?php
/**
 * User: alex
 * Date: 3/27/18
 * Time: 10:35 AM
 */

namespace AppBundle\Form;


use AppBundle\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Enter feedback'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class
        ]);
    }

}