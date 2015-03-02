<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 09.10.14
 * Time: 12:50
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * NOTICE: this type is not used and tested yet.
 */
class TimeType extends AbstractDateTimePickerType
{
    protected function getDateFormat($options)
    {
        return \IntlDateFormatter::NONE;
    }

    protected function getTimeFormat($options)
    {
        return $options['with_seconds'] ? \IntlDateFormatter::MEDIUM : \IntlDateFormatter::SHORT;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'minute_steps' => 5
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        if ($options['widget'] !== 'single_text') {
            return;
        }

        $attr =& $view->vars['attr'];
        $attr['step'] = $options['with_seconds'] ? '1' : $options['minute_steps'] * 60;

        if ($this->prefereHtml5Picker()) {
            return;
        }

        $attr['data-pick-date'] = 'false';

        $attr['data-use-seconds'] = $options['with_seconds'] ? 'true' : 'false';
        $attr['data-use-minutes'] = $options['with_minutes'] ? 'true' : 'false';

        $attr['data-minute-stepping'] = $options['with_seconds'] ? false : $options['minute_steps'];
    }

    public function getParent()
    {
        return 'time';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_time';
    }
}