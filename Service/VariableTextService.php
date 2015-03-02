<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.11.14
 * Time: 15:23
 */

namespace Hn\BootstrapLayoutBundle\Service;


class VariableTextService implements VariableTextServiceInterface
{
    /**
     * @param string $text
     * @param array $variables
     * @param string $pattern
     * @param string $escape
     * @return string
     */
    public function insertVariables($text, array $variables, $pattern = '/\{\s*([^\}]+)\s*\}/', $escape = 'htmlspecialchars')
    {
        return preg_replace_callback($pattern, function ($match) use ($variables, $escape) {
            $variableName = end($match);
            if (!array_key_exists($variableName, $variables)) {
                return '{' . $variableName . '}';
            }

            return call_user_func($escape, $variables[$variableName]);
        }, $text);
    }
}