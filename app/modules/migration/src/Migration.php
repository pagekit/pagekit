<?php

namespace Pagekit\Migration;

class Migration
{
    /**
     * @var array[]
     */
    protected $versions = [];

    /**
     * @var string
     */
    protected $current;

    /**
     * Constructor.
     *
     * @param array  $versions
     * @param string $current
     */
    public function __construct($versions = array(), $current = null)
    {
        $this->versions = $versions;
        $this->current  = $current;

        uksort($this->versions, 'strnatcmp');
    }

    /**
     * Gets migration versions.
     *
     * @param  string|null $version
     * @param  string      $method
     * @return array
     */
    public function get($version = null, $method = 'up')
    {
        if ($method == 'up') {
            $versions = $this->load($this->current, $version);
        } else {
            $versions = $this->load($version, $this->current, 'down');
        }

        return array_keys($versions);
    }

    /**
     * Migrate to a version.
     *
     * @param  string|null $version
     * @return string|bool
     */
    public function run($version = null)
    {
        if (is_null($version) || is_null($this->current) || strnatcmp($this->current, $version) < 0) {
            $vers = $this->apply($this->load($this->current, $version));
        } else {
            $vers = $this->apply($this->load($version, $this->current, 'down'), 'down');
        }

        return $vers;
    }

    /**
     * Applies migrations.
     *
     * @param  array  $versions
     * @param  string $method
     * @return string|bool
     */
    protected function apply(array $versions, $method = 'up')
    {
        $version = false;

        foreach ($versions as $version => $config) {

            if (is_array($config) && isset($config[$method])) {

                $result = call_user_func($config[$method]);

                if (is_string($result)) {
                    return $result;
                }

                if ($result === false) {
                    return $version;
                }
            }
        }

        return $version;
    }

    /**
     * Loads migrations.
     *
     * @param  string|null $start
     * @param  string|null $end
     * @param  string      $method
     * @return string|bool
     */
    protected function load($start = null, $end = null, $method = 'up')
    {
        $versions = [];

        foreach ($this->versions as $version => $config) {

            if (($start !== null && strnatcmp($start, $version) >= 0) || ($end !== null && strnatcmp($end, $version) < 0)) {
                continue;
            }

            $versions[$version] = $config;
        }


        if ($method == 'down') {
            $versions = array_reverse($versions, true);
        }

        return $versions;
    }
}
