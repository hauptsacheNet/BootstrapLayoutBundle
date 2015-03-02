<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 09.10.14
 * Time: 12:11
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimeType extends AbstractDateTimePickerType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($options['widget'] === 'single_text') {
            return;
        }

        $dateOptions = $builder->get('date')->getOptions();
        // unset the format if it is the default
        if ($dateOptions['format'] === \Symfony\Component\Form\Extension\Core\Type\DateType::HTML5_FORMAT) {
            unset($dateOptions['format']);
        }
        $builder->add('date', 'hn_bootstrap_date', $dateOptions);

        $timeOptions = $builder->get('time')->getOptions();
        $builder->add('time', 'hn_bootstrap_time', $timeOptions);
    }

    protected function getDateFormat($options)
    {
        return \IntlDateFormatter::MEDIUM;
    }

    protected function getTimeFormat($options)
    {
        return $options['with_seconds'] ? \IntlDateFormatter::MEDIUM : \IntlDateFormatter::SHORT;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $widget = function (Options $options) {
            return $this->prefereHtml5Picker() ? null : 'single_text';
        };

        $resolver->setDefaults(array(
            'widget' => $widget,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'minute_steps' => 5
        ));

        if ($this->prefereHtml5Picker()) {
            $resolver->setDefaults(array(
                'format' => \Symfony\Component\Form\Extension\Core\Type\DateTimeType::HTML5_FORMAT
            ));
        }
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

        $attr['data-use-seconds'] = $options['with_seconds'] ? 'true' : 'false';
        $attr['data-use-minutes'] = $options['with_minutes'] ? 'true' : 'false';

        $attr['data-minute-stepping'] = $options['with_seconds'] ? false : $options['minute_steps'];
    }

    public function getParent()
    {
        return 'datetime';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_datetime';
    }
}