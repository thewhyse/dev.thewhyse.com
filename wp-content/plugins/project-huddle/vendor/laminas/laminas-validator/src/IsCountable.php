<?php // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod

namespace ProjectHuddle\Vendor\Laminas\Validator;

use Traversable;

use function count;
use function is_array;
use function is_countable;
use function is_numeric;
use function sprintf;
use function ucfirst;

/**
 * Validate that a value is countable and the count meets expectations.
 *
 * The validator has five specific behaviors:
 *
 * - You can determine if a value is countable only
 * - You can test if the value is an exact count
 * - You can test if the value is greater than a minimum count value
 * - You can test if the value is greater than a maximum count value
 * - You can test if the value is between the minimum and maximum count values
 *
 * When creating the instance or calling `setOptions()`, if you specify a
 * "count" option, specifying either "min" or "max" leads to an inconsistent
 * state and, as such will raise an Exception\InvalidArgumentException.
 */
class IsCountable extends AbstractValidator
{
    public const NOT_COUNTABLE = 'notCountable';
    public const NOT_EQUALS    = 'notEquals';
    public const GREATER_THAN  = 'greaterThan';
    public const LESS_THAN     = 'lessThan';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_COUNTABLE => 'The input must be an array or an instance of \\Countable',
        self::NOT_EQUALS    => "The input count must equal '%count%'",
        self::GREATER_THAN  => "The input count must be less than '%max%', inclusively",
        self::LESS_THAN     => "The input count must be greater than '%min%', inclusively",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'count' => ['options' => 'count'],
        'min'   => ['options' => 'min'],
        'max'   => ['options' => 'max'],
    ];

    /**
     * Options for the between validator
     *
     * @var array
     */
    protected $options = [
        'count' => null,
        'min'   => null,
        'max'   => null,
    ];

    /**
     * @param array|Traversable $options
     * @return $this Provides fluid interface
     */
    public function setOptions($options = [])
    {
        foreach (['count', 'min', 'max'] as $option) {
            if (! is_array($options) || ! isset($options[$option])) {
                continue;
            }

            $method = sprintf('set%s', ucfirst($option));
            $this->$method($options[$option]);
            unset($options[$option]);
        }

        return parent::setOptions($options);
    }

    /**
     * Returns true if and only if $value is countable (and the count validates against optional values).
     *
     * @param  iterable $value
     * @return bool
     */
    public function isValid($value)
    {
        if (! is_countable($value)) {
            $this->error(self::NOT_COUNTABLE);
            return false;
        }

        $count = count($value);

        if (is_numeric($this->getCount())) {
            if ($count !== $this->getCount()) {
                $this->error(self::NOT_EQUALS);
                return false;
            }

            return true;
        }

        if (is_numeric($this->getMax()) && $count > $this->getMax()) {
            $this->error(self::GREATER_THAN);
            return false;
        }

        if (is_numeric($this->getMin()) && $count < $this->getMin()) {
            $this->error(self::LESS_THAN);
            return false;
        }

        return true;
    }

    /**
     * Returns the count option
     *
     * @return mixed
     */
    public function getCount()
    {
        return $this->options['count'];
    }

    /**
     * Returns the min option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->options['min'];
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->options['max'];
    }

    /**
     * @return void
     * @throws Exception\InvalidArgumentException If either a min or max option
     *     was previously set.
     */
    private function setCount(mixed $value)
    {
        if (isset($this->options['min']) || isset($this->options['max'])) {
            throw new Exception\InvalidArgumentException(
                'Cannot set count; conflicts with either a min or max option previously set'
            );
        }
        $this->options['count'] = $value;
    }

    /**
     * @return void
     * @throws Exception\InvalidArgumentException If either a count or max option
     *     was previously set.
     */
    private function setMin(mixed $value)
    {
        if (isset($this->options['count'])) {
            throw new Exception\InvalidArgumentException(
                'Cannot set count; conflicts with either a count option previously set'
            );
        }
        $this->options['min'] = $value;
    }

    /**
     * @return void
     * @throws Exception\InvalidArgumentException If either a count or min option
     *     was previously set.
     */
    private function setMax(mixed $value)
    {
        if (isset($this->options['count'])) {
            throw new Exception\InvalidArgumentException(
                'Cannot set count; conflicts with either a count option previously set'
            );
        }
        $this->options['max'] = $value;
    }
}
