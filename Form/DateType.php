<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 09.10.14
 * Time: 11:47
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateType extends AbstractDateTimePickerType
{
    protected function getDateFormat($options)
    {
        return \IntlDateFormatter::MEDIUM;
    }

    protected function getTimeFormat($options)
    {
        return \IntlDateFormatter::NONE;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        if ($options['widget'] !== 'single_text') {
            return;
        }

        if ($this->prefereHtml5Picker()) {
            return;
        }

        $attr =& $view->vars['attr'];
        $attr['data-pick-time'] = 'false';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        if ($this->prefereHtml5Picker()) {
            $resolver->setDefaults(array(
                'format' => \Symfony\Component\Form\Extension\Core\Type\DateType::HTML5_FORMAT
            ));
        }
    }

    public function getParent()
    {
        return 'date';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_date';
    }
}