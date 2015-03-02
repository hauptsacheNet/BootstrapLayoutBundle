<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 01.12.14
 * Time: 10:12
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Detection\MobileDetect;
use Hn\BootstrapLayoutBundle\Service\DatePatternService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractDateTimePickerType extends AbstractType
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var DatePatternService
     */
    protected $datePatternService;

    /**
     * @var MobileDetect
     */
    protected $mobileDetect;

    public function __construct(RequestStack $requestStack, DatePatternService $datePatternService, MobileDetect $mobileDetect)
    {
        $this->requestStack = $requestStack;
        $this->datePatternService = $datePatternService;
        $this->mobileDetect = $mobileDetect;
    }

    abstract protected function getDateFormat($options);

    abstract protected function getTimeFormat($options);

    protected function getDateFormatter($options, $locale = null)
    {
        $dateFormat = $this->getDateFormat($options);
        $timeFormat = $this->getTimeFormat($options);
        return $this->datePatternService->getFormatter($dateFormat, $timeFormat, $locale);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $format = function (Options $options) {
            return $this->getDateFormatter($options)->getPattern();
        };

        $todayInRange = function (Options $options) {
            $now = new \DateTime();
            return $now >= $options['min_date'] && $now <= $options['max_date'];
        };

        $defaultDate = function (Options $options) {
            if (!$options['required']) {
                return '';
            }

            $now = new \DateTime();

            if ($now < $options['min_date']) {
                return $options['min_date'];
            }

            if ($now > $options['max_date']) {
                return $options['max_date'];
            }

            return $now;
        };

        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'today_btn' => $todayInRange,
            'today_highlight' => $todayInRange,
            'format' => $format,
            'side_by_side' => true,

            'min_date' => new \DateTime('1.1.1900'),
            'max_date' => new \DateTime('+100 years'),
            'default_date' => $defaultDate,

            'disabled_dates' => array(),
            'enabled_dates' => array()
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['widget'] !== 'single_text') {
            return;
        }

        if ($this->prefereHtml5Picker()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $dateFormatter = $this->getDateFormatter($options, $request->getLocale());
        $attr =& $view->vars['attr'];

        $attr['data-language'] = $request !== null ? $request->getLocale() : 'en';
        $attr['data-format'] = $this->datePatternService->convertIcuToMoment($options['format']);
        $attr['data-side-by-side'] = $options['side_by_side'] ? 'true' : false;
        $attr['data-use-strict'] = 'true';

        $attr['data-today-btn'] = $options['today_btn'] ? 'linked' : false;
        $attr['data-today-highlight'] = $options['today_highlight'] ? 'true' : false;

        $attr['data-min-date'] = $dateFormatter->format($options['min_date']);
        $attr['data-max-date'] = $dateFormatter->format($options['max_date']);
        $attr['data-default-date'] = $dateFormatter->format($options['default_date']);

        $formatCallback = array($dateFormatter, 'format');
        $attr['disabled-dates'] = json_encode(array_map($formatCallback, $options['disabled_dates']));
        $attr['enabled-dates'] = json_encode(array_map($formatCallback, $options['enabled_dates']));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$this->prefereHtml5Picker()) {
            // for some reason this does not work on build view
            $view->vars['type'] = 'text';
        }
    }

    protected function prefereHtml5Picker()
    {
        return $this->mobileDetect->isMobile();
    }
}