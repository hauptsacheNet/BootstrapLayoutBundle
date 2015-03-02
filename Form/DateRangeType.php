<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 02.12.14
 * Time: 12:51
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $baseOptions = array('required' => $options['required']);

        if ($options['min_date'] !== null) {
            $baseOptions['min_date'] = $options['min_date'];
        }
        if ($options['max_date'] !== null) {
            $baseOptions['max_date'] = $options['max_date'];
        }

        $startOptions = array_merge($baseOptions, $options['options'], $options['start_options']);
        $endOptions = array_merge($baseOptions, $options['options'], $options['end_options']);

        $builder->add($options['start_field'], $options['type'], $startOptions);
        $builder->add($options['end_field'], $options['type'], $endOptions);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('start_field', 'end_field'));

        $resolver->setDefaults(array(
            'inherit_data' => true,
            'type' => 'hn_bootstrap_datetime',
            'min_date' => null,
            'max_date' => null,

            'options' => array(),
            'start_options' => array(),
            'end_options' => array()
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr =& $view->vars['attr'];

        $className = array_key_exists('class', $attr) ? $attr['class'] : '';
        $attr['class'] = trim($className . ' date-range-type');

        $view->vars['start_field'] = $options['start_field'];
        $view->vars['end_field'] = $options['end_field'];
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_date_range';
    }
}