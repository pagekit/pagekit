<?php

namespace Pagekit\Package\Loader;

use Pagekit\Package\Exception\InvalidArgumentException;
use Pagekit\Package\Exception\UnexpectedValueException;
use Pagekit\Package\Version\VersionValidator;

class ArrayLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($config, $class = 'Pagekit\Package\Package')
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException('Package config needs to be an array.');
        }

        if (!isset($config['name'])) {
            throw new UnexpectedValueException('Unknown package has no name defined ('.json_encode($config).').');
        }

        if (!isset($config['version'])) {
            throw new UnexpectedValueException('Package "'.$config['name'].'" has no version defined.');
        }

        if (!VersionValidator::validate($config['version'])) {
            throw new UnexpectedValueException('Package "'.$config['name'].'" has invalid version defined "'.$config['version'].'".');
        }

        $package = new $class($config['name'], $config['version']);
        $package->setType($config['type']);

        if (!empty($config['title']) && is_string($config['title'])) {
            $package->setTitle($config['title']);
        }

        if (!empty($config['description']) && is_string($config['description'])) {
            $package->setDescription($config['description']);
        }

        if (!empty($config['keywords']) && is_array($config['keywords'])) {
            $package->setKeywords($config['keywords']);
        }

        if (!empty($config['homepage']) && is_string($config['homepage'])) {
            $package->setHomepage($config['homepage']);
        }

        if (!empty($config['license'])) {
            $package->setLicense(is_array($config['license']) ? $config['license'] : [$config['license']]);
        }

        if (!empty($config['authors']) && is_array($config['authors'])) {
            $package->setAuthors($config['authors']);
        }

        if (isset($config['extra']) && is_array($config['extra'])) {
            $package->setExtra($config['extra']);
        }

        if (!empty($config['time'])) {
            try {
                $package->setReleaseDate(new \DateTime($config['time'], new \DateTimeZone('UTC')));
            } catch (\Exception $e) {}
        }

        if (isset($config['source'])) {
            if (!isset($config['source']['type']) || !isset($config['source']['url'])) {
                throw new UnexpectedValueException(sprintf("Package source should be specified as {\"type\": ..., \"url\": ...},\n%s given", json_encode($config['source'])));
            }
            $package->setSourceType($config['source']['type']);
            $package->setSourceUrl($config['source']['url']);
        }

        if (isset($config['dist'])) {
            if (!isset($config['dist']['type']) || !isset($config['dist']['url'])) {
                throw new UnexpectedValueException(sprintf("Package dist should be specified as {\"type\": ..., \"url\": ...},\n%s given", json_encode($config['dist'])));
            }
            $package->setDistType($config['dist']['type']);
            $package->setDistUrl($config['dist']['url']);
            $package->setDistSha1Checksum(isset($config['dist']['shasum']) ? $config['dist']['shasum'] : null);
        }

        if (!empty($config['autoload']) && is_array($config['autoload'])) {
            $package->setAutoload($config['autoload']);
        }

        if (!empty($config['resources']) && is_array($config['resources'])) {
            $package->setResources($config['resources']);
        }

        return $package;
    }
}
