<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 03.12.14
 * Time: 17:15
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TabType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
            'fade_effect' => false,
            'active' => null,

            'save' => true,
            'save_duration' => 1, // day
            'save_path' => null
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['fade_effect'] = $options['fade_effect'];
        $view->vars['active'] = $options['active'];

        // persistent tab attributes
        $attr =& $view->vars['attr'];
        if ($options['save']) {
            $id = $view->vars['id'];
            $attr['data-save-name'] = $id;
            $attr['data-save-duration'] = $options['save_duration'];
            $attr['data-save-path'] = $options['save_path'] ?: false;

            // activate tab that was saved to be active
            $request = $this->requestStack->getCurrentRequest();
            $cookieValue = $request !== null ? $request->cookies->get($id) : null;
            if ($form->offsetExists($cookieValue)) {
                $view->vars['active'] = $cookieValue;
            }
        }

        // activate first tab with error
        $tabsWithErrors = $this->generateTabErrorList($form);
        $view->vars['tabs_with_errors'] = $tabsWithErrors;
        if (!empty($tabsWithErrors)) {
            $view->vars['active'] = reset($tabsWithErrors);
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->stripRequiredAttribute($view);
    }

    protected function stripRequiredAttribute(FormView $view)
    {
        if (isset($view->vars['required'])) {
            $view->vars['required'] = false;
        }

        foreach ($view as $childView) {
            $this->stripRequiredAttribute($childView);
        }
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function generateTabErrorList(FormInterface $form)
    {
        $tabsWithError = array();
        /** @var FormInterface $child */
        foreach ($form as $name => $child) {
            if (count($child->getErrors(true)) === 0) {
                continue;
            }

            $tabsWithError[] = $name;
        }
        return $tabsWithError;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_tab';
    }
}