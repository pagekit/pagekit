<?php

namespace Pagekit\Filter;

/**
 * This filter performs a regular expression search and replace.
 *
 * @link http://php.net/manual/en/function.preg-replace.php
 */
class PregReplaceFilter extends AbstractFilter
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = [
            'pattern' => null,
            'replacement' => '',
        ];
    }

    /**
     * Returns the regex pattern.
     *
     * @return string|array
     */
    public function getPattern()
    {
        return $this->options['pattern'];
    }

    /**
     * Set the regex pattern.
     *
     * @param  string|array $pattern
     * @throws \InvalidArgumentException
     */
    public function setPattern($pattern)
    {
        if (!is_array($pattern) && !is_string($pattern)) {
            $pattern = is_object($pattern) ? get_class($pattern) : gettype($pattern);
            throw new \InvalidArgumentException(sprintf('%s expects pattern to be array or string; received "%s"', __METHOD__, $pattern));
        }

        if (is_array($pattern)) {
            foreach ($pattern as $p) {
                $this->validatePattern($p);
            }
        }

        if (is_string($pattern)) {
            $this->validatePattern($pattern);
        }

        $this->options['pattern'] = $pattern;
    }

    /**
     * Returns the replacement value.
     *
     * @return string|array
     */
    public function getReplacement()
    {
        return $this->options['replacement'];
    }

    /**
     * Sets the replacement array/string
     *
     * @param  array|string $replacement
     * @throws \InvalidArgumentException
     */
    public function setReplacement($replacement)
    {
        if (!is_array($replacement) && !is_string($replacement)) {
            $replacement = is_object($replacement) ? get_class($replacement) : gettype($replacement);
            throw new \InvalidArgumentException(sprintf('%s expects replacement to be array or string; received "%s"', __METHOD__, $replacement));
        }

        $this->options['replacement'] = $replacement;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        if ($this->options['pattern'] === null) {
            throw new \RuntimeException(sprintf('Filter %s does not have a valid pattern set', get_called_class()));
        }

        return preg_replace($this->options['pattern'], $this->options['replacement'], $value);
    }

    /**
     * Validate a pattern and ensure it does not contain the "e" modifier.
     *
     * @param  string $pattern
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function validatePattern($pattern)
    {
        if (!preg_match('/(?<modifier>[imsxeADSUXJu]+)$/', $pattern, $matches)) {
            return true;
        }

        if (false !== strstr($matches['modifier'], 'e')) {
            throw new \InvalidArgumentException(sprintf('Pattern for a PregReplace filter may not contain the "e" pattern modifier; received "%s"', $pattern));
        }
    }
}
