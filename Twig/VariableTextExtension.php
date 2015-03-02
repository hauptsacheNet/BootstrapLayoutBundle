<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.11.14
 * Time: 15:31
 */

namespace Hn\BootstrapLayoutBundle\Twig;


use Hn\BootstrapLayoutBundle\Service\VariableTextServiceInterface;

class VariableTextExtension extends \Twig_Extension
{
    /**
     * @var VariableTextServiceInterface
     */
    private $variableTextService;

    public function __construct(VariableTextServiceInterface $variableTextService)
    {
        $this->variableTextService = $variableTextService;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('insertVariables', array($this->variableTextService, 'insertVariables'))
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'hn_bootstrap_variable_text';
    }
}