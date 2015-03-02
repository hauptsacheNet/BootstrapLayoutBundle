<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 07.11.14
 * Time: 16:29
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Hn\BootstrapLayoutBundle\Form\DataTransformer\ArrayKeyDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextVariablesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $variableMap = $this->generateVariableMap($options);

        foreach ($variableMap as $index => $variableName) {

            $fieldOptions = array_merge($options['field_options'], array(
                'label' => $variableName
            ));

            // use the index and not the name to prevent bad names
            $builder->add($index, $options['field_type'], $fieldOptions);
        }


        $transformer = new ArrayKeyDataTransformer($variableMap, !$options['ignore_leftover_keys']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('text'));
        $resolver->setDefaults(array(
            'variable_pattern' => '/\{\s*([^\}]+)\s*\}/',
            'allow_html' => false,

            'field_type' => 'text',
            'field_options' => array(
                // this prevents the labels to be accidentally translated
                'translation_domain' => 'variable_names'
            ),

            'ignore_leftover_keys' => true
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $text = $options['text'];
        $text = $options['allow_html'] ? $text : htmlspecialchars($text);

        $data = $form->getData();

        $text = preg_replace_callback($options['variable_pattern'], function ($match) use ($data) {
            $key = end($match);

            $initial = !array_key_exists($key, $data);
            $content = !$initial ? $data[$key] : $key;

            $attr = $initial ? ' class="initial"' : '';
            $tag = '<span data-variable="' . htmlspecialchars($key) . '"' . $attr . '>';
            return $tag . htmlspecialchars($content) . '</span>';
        }, $text);
        $view->vars['text'] = $text;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $variableMap = $this->generateVariableMap($options);

        foreach ($variableMap as $index => $variableName) {
            $view->children[$index]->vars['attr']['data-variable-field'] = $variableName;
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_text_variables';
    }

    /**
     * @param array $options
     * @return array
     */
    protected function generateVariableMap(array $options)
    {
        $text = $options['allow_html'] ? $options['text'] : htmlspecialchars($options['text']);

        $variableMap = array(/* "name", "date", "other_strings" */);
        preg_match_all($options['variable_pattern'], $text, $variableMap, PREG_SET_ORDER);
        $variableMap = array_map('end', $variableMap);

        return array_unique($variableMap);
    }
}