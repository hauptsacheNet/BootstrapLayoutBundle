<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.11.14
 * Time: 15:23
 */

namespace Hn\BootstrapLayoutBundle\Service;


interface VariableTextServiceInterface
{
    /**
     * @param string $text
     * @param array $variables
     * @param string $pattern
     * @param string $escape
     * @return string
     */
    public function insertVariables($text, array $variables, $pattern = '/\{\s*([^\}]+)\s*\}/', $escape = 'htmlspecialchars');
}