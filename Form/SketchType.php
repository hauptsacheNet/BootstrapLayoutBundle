<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 26.11.14
 * Time: 15:07
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SketchType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'width' => 800,
            'height' => 340,
            'quality' => 25, // higher quality is not required

            // small sizes apply to preview if use_modal is true
            'small_width' => 80,
            'small_height' => 34,

            'background_color' => '#ffffff', // not fully implemented
            'default_color' => '#000000',
            'default_size' => 5,

            'file_attr' => array(),
            'use_modal' => true,
            'modal_width' => function (Options $options) {
                return $options['width'] + 15 * 2;
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $toVars = array(
            'width',
            'height',
            'quality',
            'small_width',
            'small_height',
            'background_color',
            'default_color',
            'default_size',
            'file_attr',
            'use_modal',
            'modal_width'
        );
        foreach ($toVars as $property) {
            $view->vars[$property] = $options[$property];
        }

        $class = array_key_exists('class', $view->vars['attr']) ? $view->vars['attr']['class'] : '';

        $view->vars['attr']['class'] = $class = trim($class . ' sketch-type');

        if ($options['read_only'] || $options['disabled']) {
            $view->vars['attr']['class'] = trim($class . ' read-only');
        }
    }

    public function getParent()
    {
        return 'hidden';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_sketch';
    }
}