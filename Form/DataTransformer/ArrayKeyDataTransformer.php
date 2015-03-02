<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 07.11.14
 * Time: 17:03
 */

namespace Hn\BootstrapLayoutBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayKeyDataTransformer implements DataTransformerInterface
{
    /**
     * The key contains what the value will be transformed to
     * The value contains the original representation of the key.
     *
     * This allows to make easy transfomations with arrays like:
     * array("foo", "bar") etc without having to specify the keys directly.
     *
     * @var array
     */
    private $map;

    /**
     * If true, the implementation will throw an exception if keys cannot be mapped
     *
     * @var boolean
     */
    private $yell;

    /**
     * @param array $map
     */
    public function __construct(array $map, $yell = true)
    {
        $this->map = $map;
        $this->yell = $yell;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is submitted using {@link Form::submit()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and submit '2009-10-10'
     *    you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if ($value === null) {
            return array();
        }

        $values = $this->makeArray($value);
        return $this->transformUsing(array_flip($this->map), $values);
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as empty strings). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if ($value === '' || $value === null) {
            return array();
        }

        $values = $this->makeArray($value);
        return $this->transformUsing($this->map, $values);
    }

    /**
     * @param $value
     * @return array
     */
    protected function makeArray($value)
    {
        if ($value instanceof \Traversable) {
            $value = iterator_to_array($value);
        }

        if (!is_array($value)) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new TransformationFailedException("Only arrays or traversable are supported, got $type");
        }
        return $value;
    }

    /**
     * @param array $map
     * @param array $values
     * @return array
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    protected function transformUsing($map, $values)
    {
        $result = array();

        foreach ($values as $key => $value) {

            if (!array_key_exists($key, $map)) {

                if (!$this->yell) {
                    continue;
                }

                $expected = implode(', ', array_keys($map));
                $msg = "The key '$key' wasn't mapped for transformation, expected $expected";
                throw new TransformationFailedException($msg);
            }

            $result[$map[$key]] = $value;
        }

        return $result;
    }
}