<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 04.12.14
 * Time: 20:30
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Detection\MobileDetect;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChoiceTypeExtension extends AbstractTypeExtension
{
    /**
     * @var MobileDetect
     */
    protected $mobileDetect;

    public function __construct(MobileDetect $mobileDetect)
    {
        $this->mobileDetect = $mobileDetect;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $select2 = function (Options $options) {
            return $options['multiple'] && !$options['expanded'];
        };

        $sortList = function (Options $options) {
            // expect that an object choice list should be sorted
            return $options['choice_list'] instanceof ObjectChoiceList ? SORT_ASC : false;
        };

        $resolver->setDefaults(array(
            'select2' => $select2,
            'select2_mobile' => false,
            'sort_list' => $sortList
        ));

        $resolver->setAllowedValues(array(
            'select2' => array(true, false),
            'select2_mobile' => array(true, false),
            'sort_list' => array(SORT_ASC, SORT_DESC, false)
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['sort_list']) {
            $this->sortChoices($view->vars['choices'], $options['sort_list']);
        }

        if ($options['expanded']) {
            return;
        }

        $attr =& $view->vars['attr'];
        $className = array_key_exists('class', $attr) ? $attr['class'] : '';
        if (strstr($className, 'js-select2') !== false) {
            $msg = "you shouldn't set the js-select2 class manually. Use the option 'select2' instead";
            trigger_error($msg, E_USER_WARNING);
        }

        if (!$options['select2']) {
            return;
        }

        if ($options['select2_mobile'] || $this->mobileDetect->isMobile()) {
            return;
        }

        $attr['class'] = trim($className . ' js-select2');
    }

    protected function sortChoices(&$choices, $direction)
    {
        // first sort by key which will sort optgroups
        ksort($choices, $direction);


        // sort the first level values
        $sortMod = $direction === SORT_DESC ? -1 : 1;
        uasort($choices, function ($a, $b) use ($sortMod) {
            // show optgroup before normal options
            $aIsArray = is_array($a);
            $bIsArray = is_array($b);
            $typediff = $aIsArray - $bIsArray;

            if (($aIsArray && $bIsArray) || $typediff !== 0) {
                return $typediff * $sortMod;
            }

            if (!$a instanceof ChoiceView) {
                $type = is_object($a) ? get_class($a) : gettype($a);
                throw new \RuntimeException("Expected ChoiceView, got $type");
            }

            if (!$b instanceof ChoiceView) {
                $type = is_object($b) ? get_class($b) : gettype($b);
                throw new \RuntimeException("Expected ChoiceView, got $type");
            }

            return strcmp($a->label, $b->label) * $sortMod;
        });

        // sort also inside choices groups
        foreach ($choices as $choice) {
            if (!is_array($choice)) {
                continue;
            }

            $this->sortChoices($choice, $direction);
        }
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'choice';
    }
}