<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 13.11.14
 * Time: 09:36
 */

namespace Hn\BootstrapLayoutBundle\Service;


use IntlDateFormatter;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DatePatternService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * @param string|null $locale
     * @return string
     */
    protected function getLocale($locale = null)
    {
        if ($locale !== null) {
            return (string)$locale;
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            return $request->getLocale();
        }

        $msg = "The Dateformat service requires a locale (either though request or parameter)";
        trigger_error(E_USER_WARNING, $msg);
        $this->logger->warning($msg);

        return 'en';
    }

    /**
     * @param int $dateType
     * @param int $timeType
     * @param string|null $locale
     * @return IntlDateFormatter
     */
    public function getFormatter($dateType = IntlDateFormatter::MEDIUM, $timeType = IntlDateFormatter::MEDIUM, $locale = null)
    {
        return new IntlDateFormatter($this->getLocale($locale), $dateType, $timeType);
    }

    /**
     * @param int $dateType
     * @param int $timeType
     * @param string|null $locale
     * @return string
     */
    public function getPattern($dateType = IntlDateFormatter::MEDIUM, $timeType = IntlDateFormatter::MEDIUM, $locale = null)
    {
        return $this->getFormatter($dateType, $timeType, $locale)->getPattern();
    }

    /**
     * @param int $dateType
     * @param string|null $locale
     * @return string
     */
    public function getDatePattern($dateType = IntlDateFormatter::MEDIUM, $locale = null)
    {
        return $this->getPattern($dateType, IntlDateFormatter::NONE, $locale);
    }

    /**
     * @param int $timeType
     * @param string|null $locale
     * @return string
     */
    public function getTimePattern($timeType = IntlDateFormatter::MEDIUM, $locale = null)
    {
        return $this->getPattern(IntlDateFormatter::NONE, $timeType, $locale);
    }

    /**
     * @param string $pattern
     * @return string
     */
    public function convertIcuToMoment($pattern)
    {
        $pattern = str_replace('dd', 'DD', $pattern);
        $pattern = str_replace('d', 'D', $pattern);
        $pattern = preg_replace('/[eEc]{4,}/', 'dd', $pattern);
        $pattern = preg_replace('/[eEc]{1,3}/', 'd', $pattern);

        $pattern = str_replace('MMMM', 'MMMM', $pattern);
        $pattern = str_replace('MMM', 'MMM', $pattern);
        $pattern = str_replace('MM', 'MM', $pattern);
        $pattern = str_replace('M', 'M', $pattern);

        $pattern = preg_replace('/(^|[^y])y([^y]|$)/', '\\1YYYY\\2', $pattern);
        $pattern = preg_replace('/(^|[^y])yyy([^y]|$)/', '\\1YY\\2', $pattern);
        $pattern = preg_replace('/(^|[^y])yyyy([^y]|$)/', '\\1YYYY\\2', $pattern);

        return $pattern;
        // TODO this replace requires much more work
        // this is what symfony/php takes: http://www.icu-project.org/apiref/icu4c/classSimpleDateFormat.html#details
        // this is what the datepicker takes: http://bootstrap-datepicker.readthedocs.org/en/release/options.html#format
        // also this is what datetimepicker takes: http://momentjs.com/docs/#/manipulating/
    }
}
