<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 22.10.14
 * Time: 17:37
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Hn\BootstrapLayoutBundle\Form\DataTransformer\ChoiceWithTextDataTransformer;
use Hn\BootstrapLayoutBundle\Helper\ChainChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Renders a choice element with an optional field which is only shown when the right option is selected.
 *
 * // minimal setup
 * $builder->add('field_to_store_choice_or_text', 'hn_bootstrap_choice_with_text', array(
 *     'choices' => array("red", "green", "blue"),
 *     'text_choice_label' => "other"
 * ))
 *
 * @package Hn\BootstrapLayoutBundle\Form
 */
class ChoiceWithTextType extends AbstractType
{
    private $choiceListCache = array();

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['single_field']) {
            $transformer = new ChoiceWithTextDataTransformer(
                $options['choices'],
                $options['text_choice_value'],
                $options['choice_name'],
                $options['text_name']
            );
            $builder->addViewTransformer($transformer);
        }

        $labels = array();
        $choices = array();

        /** @var ChoiceListInterface $choiceList */
        foreach (array($options['choice_list'], $options['text_choice_list']) as $choiceList) {

            /** @var ChoiceView $view */
            foreach ($choiceList->getRemainingViews() as $view) {
                $labels[] = $view->label;
                $choices[] = $view->data;
            }
        }

        $builder->add($options['choice_name'], 'choice', array(
            'choice_list' => new ChoiceList($choices, $labels),
            'read_only' => $options['read_only'],
            'required' => false, // later overwritten by attribute
            'expanded' => $options['expanded'],
            'empty_value' => $options['empty_value']
        ));

        $builder->add($options['text_name'], $options['text_type'], array(
            'required' => $options['required'],
            'read_only' => $options['read_only']
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceListCache = &$this->choiceListCache;
        $generateChoiceListGenerator = function ($choicesField) use (&$choiceListCache) {

            return function (Options $options) use ($choicesField, &$choiceListCache) {
                // Harden against NULL values (like in EntityType and ModelType)
                $choices = null !== $options[$choicesField] ? $options[$choicesField] : array();

                // Reuse existing choice lists in order to increase performance
                $hash = hash('sha256', serialize(array($choices, $options['preferred_choices'])));

                if (!isset($choiceListCache[$hash])) {
                    $choiceListCache[$hash] = new ChoiceList(array_keys($choices), array_values($choices), $options['preferred_choices']);
                }

                return $choiceListCache[$hash];
            };
        };

        $resolver->setDefaults(array(
            'choices' => array(),
            'preferred_choices' => array(),
            'choice_list' => $generateChoiceListGenerator('choices'),

            'text_choice_label' => 'other',
            'text_choice_value' => '_text_',
            'text_choices' => function (Options $options) {

                $textChoiceValue = $options['text_choice_value'];
                if (!is_string($textChoiceValue)) {
                    $type = is_object($textChoiceValue) ? get_class($textChoiceValue) : gettype($textChoiceValue);
                    $msg = "If the text choice value is not a string, use text_choice_list to prevent type errors";
                    trigger_error("$msg, got $type", E_USER_WARNING);
                }

                return array($options['text_choice_value'] => $options['text_choice_label']);
            },
            'text_choice_list' => $generateChoiceListGenerator('text_choices'),

            'empty_value' => function (Options $options) {
                return $options['required'] ? null : '';
            },
            'expanded' => false,
            'message_required' => true,

            'single_field' => true,
            'choice_name' => 'choice',
            'text_name' => 'text',
            'text_type' => 'text',
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ChoiceListInterface $choiceList */
        $choiceList = $form->get($options['choice_name'])->getConfig()->getOption('choice_list');

        /** @var ChoiceListInterface $textChoiceList */
        $textChoiceList = $options['text_choice_list'];

        $textValues = array_intersect($choiceList->getChoices(), $textChoiceList->getChoices());

        $view->vars['text_choice_values'] = array_map('strval', array_keys($textValues));
        $view->vars['text_choices'] = $textChoiceList->getChoices();
        $view->vars['expanded'] = $options['expanded'];
        $view->vars['message_required'] = $options['message_required'];
        $view->vars['choice_name'] = $options['choice_name'];
        $view->vars['text_name'] = $options['text_name'];
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_choice_with_text';
    }
}