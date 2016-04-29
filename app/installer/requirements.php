<?php

/**
 * Represents a single PHP requirement, e.g. an installed extension.
 * It can be a mandatory requirement or an optional recommendation.
 * There is a special subclass, named PhpIniRequirement, to check a php.ini configuration.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class Requirement
{
    private $fulfilled;
    private $testMessage;
    private $helpText;
    private $helpHtml;
    private $optional;

    /**
     * Constructor that initializes the requirement.
     *
     * @param Boolean     $fulfilled   Whether the requirement is fulfilled
     * @param string      $testMessage The message for testing the requirement
     * @param string      $helpHtml    The help text formatted in HTML for resolving the problem
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     * @param Boolean     $optional    Whether this is only an optional recommendation not a mandatory requirement
     */
    public function __construct($fulfilled, $testMessage, $helpHtml, $helpText = null, $optional = false)
    {
        $this->fulfilled = (Boolean) $fulfilled;
        $this->testMessage = (string) $testMessage;
        $this->helpHtml = (string) $helpHtml;
        $this->helpText = null === $helpText ? strip_tags($this->helpHtml) : (string) $helpText;
        $this->optional = (Boolean) $optional;
    }

    /**
     * Returns whether the requirement is fulfilled.
     *
     * @return Boolean true if fulfilled, otherwise false
     */
    public function isFulfilled()
    {
        return $this->fulfilled;
    }

    /**
     * Returns the message for testing the requirement.
     *
     * @return string The test message
     */
    public function getTestMessage()
    {
        return $this->testMessage;
    }

    /**
     * Returns the help text for resolving the problem
     *
     * @return string The help text
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    /**
     * Returns the help text formatted in HTML.
     *
     * @return string The HTML help
     */
    public function getHelpHtml()
    {
        return $this->helpHtml;
    }

    /**
     * Returns whether this is only an optional recommendation and not a mandatory requirement.
     *
     * @return Boolean true if optional, false if mandatory
     */
    public function isOptional()
    {
        return $this->optional;
    }
}

/**
 * Represents a PHP requirement in form of a php.ini configuration.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class PhpIniRequirement extends Requirement
{
    /**
     * Constructor that initializes the requirement.
     *
     * @param string           $cfgName    The configuration name used for ini_get()
     * @param Boolean|callback $evaluation Either a Boolean indicating whether the configuration should evaluate to true or false,
                                                    or a callback function receiving the configuration value as parameter to determine the fulfillment of the requirement
     * @param Boolean $approveCfgAbsence If true the Requirement will be fulfilled even if the configuration option does not exist, i.e. ini_get() returns false.
                                                    This is helpful for abandoned configs in later PHP versions or configs of an optional extension, like Suhosin.
                                                    Example: You require a config to be true but PHP later removes this config and defaults it to true internally.
     * @param string|null $testMessage The message for testing the requirement (when null and $evaluation is a Boolean a default message is derived)
     * @param string|null $helpHtml    The help text formatted in HTML for resolving the problem (when null and $evaluation is a Boolean a default help is derived)
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     * @param Boolean     $optional    Whether this is only an optional recommendation not a mandatory requirement
     */
    public function __construct($cfgName, $evaluation, $approveCfgAbsence = false, $testMessage = null, $helpHtml = null, $helpText = null, $optional = false)
    {
        $cfgValue = ini_get($cfgName);

        if (is_callable($evaluation)) {
            if (null === $testMessage || null === $helpHtml) {
                throw new InvalidArgumentException('You must provide the parameters testMessage and helpHtml for a callback evaluation.');
            }

            $fulfilled = call_user_func($evaluation, $cfgValue);
        } else {
            if (null === $testMessage) {
                $testMessage = sprintf('%s %s be %s in php.ini',
                    $cfgName,
                    $optional ? 'should' : 'must',
                    $evaluation ? 'enabled' : 'disabled'
                );
            }

            if (null === $helpHtml) {
                $helpHtml = sprintf('Set <strong>%s</strong> to <strong>%s</strong> in php.ini<a href="#phpini">*</a>.',
                    $cfgName,
                    $evaluation ? 'on' : 'off'
                );
            }

            $fulfilled = $evaluation == $cfgValue;
        }

        parent::__construct($fulfilled || ($approveCfgAbsence && false === $cfgValue), $testMessage, $helpHtml, $helpText, $optional);
    }
}

/**
 * A RequirementCollection represents a set of Requirement instances.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class RequirementCollection implements IteratorAggregate
{
    private $requirements = array();

    /**
     * Gets the current RequirementCollection as an Iterator.
     *
     * @return Traversable A Traversable interface
     */
    public function getIterator()
    {
        return new ArrayIterator($this->requirements);
    }

    /**
     * Adds a Requirement.
     *
     * @param Requirement $requirement A Requirement instance
     */
    public function add(Requirement $requirement)
    {
        $this->requirements[] = $requirement;
    }

    /**
     * Adds a mandatory requirement.
     *
     * @param Boolean     $fulfilled   Whether the requirement is fulfilled
     * @param string      $testMessage The message for testing the requirement
     * @param string      $helpHtml    The help text formatted in HTML for resolving the problem
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addRequirement($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Adds an optional recommendation.
     *
     * @param Boolean     $fulfilled   Whether the recommendation is fulfilled
     * @param string      $testMessage The message for testing the recommendation
     * @param string      $helpHtml    The help text formatted in HTML for resolving the problem
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addRecommendation($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, true));
    }

    /**
     * Adds a mandatory requirement in form of a php.ini configuration.
     *
     * @param string           $cfgName    The configuration name used for ini_get()
     * @param Boolean|callback $evaluation Either a Boolean indicating whether the configuration should evaluate to true or false,
                                                    or a callback function receiving the configuration value as parameter to determine the fulfillment of the requirement
     * @param Boolean $approveCfgAbsence If true the Requirement will be fulfilled even if the configuration option does not exist, i.e. ini_get() returns false.
                                                    This is helpful for abandoned configs in later PHP versions or configs of an optional extension, like Suhosin.
                                                    Example: You require a config to be true but PHP later removes this config and defaults it to true internally.
     * @param string      $testMessage The message for testing the requirement (when null and $evaluation is a Boolean a default message is derived)
     * @param string      $helpHtml    The help text formatted in HTML for resolving the problem (when null and $evaluation is a Boolean a default help is derived)
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addPhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence = false, $testMessage = null, $helpHtml = null, $helpText = null)
    {
        $this->add(new PhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Adds an optional recommendation in form of a php.ini configuration.
     *
     * @param string           $cfgName    The configuration name used for ini_get()
     * @param Boolean|callback $evaluation Either a Boolean indicating whether the configuration should evaluate to true or false,
                                                    or a callback function receiving the configuration value as parameter to determine the fulfillment of the requirement
     * @param Boolean $approveCfgAbsence If true the Requirement will be fulfilled even if the configuration option does not exist, i.e. ini_get() returns false.
                                                    This is helpful for abandoned configs in later PHP versions or configs of an optional extension, like Suhosin.
                                                    Example: You require a config to be true but PHP later removes this config and defaults it to true internally.
     * @param string      $testMessage The message for testing the requirement (when null and $evaluation is a Boolean a default message is derived)
     * @param string      $helpHtml    The help text formatted in HTML for resolving the problem (when null and $evaluation is a Boolean a default help is derived)
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addPhpIniRecommendation($cfgName, $evaluation, $approveCfgAbsence = false, $testMessage = null, $helpHtml = null, $helpText = null)
    {
        $this->add(new PhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence, $testMessage, $helpHtml, $helpText, true));
    }

    /**
     * Adds a requirement collection to the current set of requirements.
     *
     * @param RequirementCollection $collection A RequirementCollection instance
     */
    public function addCollection(RequirementCollection $collection)
    {
        $this->requirements = array_merge($this->requirements, $collection->all());
    }

    /**
     * Returns both requirements and recommendations.
     *
     * @return array Array of Requirement instances
     */
    public function all()
    {
        return $this->requirements;
    }

    /**
     * Returns all mandatory requirements.
     *
     * @return array Array of Requirement instances
     */
    public function getRequirements()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if (!$req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Returns the mandatory requirements that were not met.
     *
     * @return array Array of Requirement instances
     */
    public function getFailedRequirements()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if (!$req->isFulfilled() && !$req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Returns all optional recommmendations.
     *
     * @return array Array of Requirement instances
     */
    public function getRecommendations()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if ($req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Returns the recommendations that were not met.
     *
     * @return array Array of Requirement instances
     */
    public function getFailedRecommendations()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if (!$req->isFulfilled() && $req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Returns whether a php.ini configuration is not correct.
     *
     * @return Boolean php.ini configuration problem?
     */
    public function hasPhpIniConfigIssue()
    {
        foreach ($this->requirements as $req) {
            if (!$req->isFulfilled() && $req instanceof PhpIniRequirement) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the PHP configuration file (php.ini) path.
     *
     * @return string|false php.ini file path
     */
    public function getPhpIniConfigPath()
    {
        return get_cfg_var('cfg_file_path');
    }
}

/**
 * This class specifies all Pagekit requirements and optional recommendations.
 *
 * @author Tobias Schultze <http://tobion.de>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class PagekitRequirements extends RequirementCollection
{
    const REQUIRED_PHP_VERSION = '5.5.9';

    /**
     * Constructor that initializes the requirements.
     */
    public function __construct($path)
    {
        /* mandatory requirements follow */

        $installedPhpVersion = phpversion();

        $this->addPhpIniRequirement('detect_unicode', false);
        $this->addPhpIniRequirement('allow_url_fopen', true);

        $this->addRequirement(
            version_compare($installedPhpVersion, self::REQUIRED_PHP_VERSION, '>='),
            sprintf('PHP version must be at least %s (%s installed)', self::REQUIRED_PHP_VERSION, $installedPhpVersion),
            sprintf('You are running PHP version "<strong>%s</strong>", but Pagekit needs at least PHP "<strong>%s</strong>" to run.
                Before using Pagekit, upgrade your PHP installation, preferably to the latest version.',
                $installedPhpVersion, self::REQUIRED_PHP_VERSION),
            sprintf('Install PHP %s or newer (installed version is %s)', self::REQUIRED_PHP_VERSION, $installedPhpVersion)
        );

        $this->addRequirement(
            function_exists('json_encode'),
            'json_encode() must be available',
            'Install and enable the <strong>JSON</strong> extension.'
        );

        $this->addRequirement(
            extension_loaded('openssl'),
            'OpenSSL must be available',
            'Install and enable the <strong>OpenSSL</strong> extension.'
        );

        $this->addRequirement(
            function_exists('session_start'),
            'session_start() must be available',
            'Install and enable the <strong>session</strong> extension.'
        );

        $this->addRequirement(
            function_exists('ctype_alpha'),
            'ctype_alpha() must be available',
            'Install and enable the <strong>ctype</strong> extension.'
        );

        $this->addRequirement(
            function_exists('token_get_all'),
            'token_get_all() must be available',
            'Install and enable the <strong>Tokenizer</strong> extension.'
        );

        $this->addRequirement(
            function_exists('simplexml_import_dom'),
            'simplexml_import_dom() must be available',
            'Install and enable the <strong>SimpleXML</strong> extension.'
        );

        $this->addRequirement(
            function_exists('dom_import_simplexml'),
            'dom_import_simplexml() must be available',
            'Install and enable the <strong>DOM</strong> extension.'
        );

        $this->addRequirement(
            function_exists('mb_strtolower'),
            'mb_strtolower() must be available',
            'Install and enable the <strong>mbstring</strong> extension.'
        );

        $this->addRequirement(
            defined('PCRE_VERSION'),
            'PCRE extension must be available',
            'Install the <strong>PCRE</strong> extension (version 8.0+).'
        );

        $this->addRequirement(
            class_exists('ZipArchive'),
            'ZipArchive must be available',
            'Install and enable the <strong>ZIP</strong> extension.'
        );

        $this->addRequirement(
            class_exists('PDO'),
            'PDO must be available',
            'Install and enable the <strong>PDO</strong> extension.'
        );

        if (version_compare($installedPhpVersion, '5.6', '>=') && version_compare($installedPhpVersion, '7.0.0', '<')) {
            $this->addRequirement(!(ini_get('display_startup_errors') === "1" && ini_get('always_populate_raw_post_data') !== "-1"),
                '\'display_startup_errors\' is enabled and \'always_populate_raw_post_data\' is not set to \'-1\'',
                'Disable startup errors or set \'always_populate_raw_post_data\' to \'-1\' in php.ini.'
            );
        }

        if (class_exists('PDO')) {
            $drivers = PDO::getAvailableDrivers();
            $this->addRequirement(
                (in_array('mysql', $drivers) || in_array('sqlite', $drivers)),
                sprintf('PDO should have MySQL or SQLite drivers installed (currently available: %s)', count($drivers) ? implode(', ', $drivers) : 'none'),
                'Install <strong>PDO drivers</strong>.'
            );
        }

        $writable_directories = ["$path/tmp", "$path/tmp/cache", "$path/tmp/logs", "$path/tmp/sessions"];

        // If config.php doesn't exist, we need the root directory of the app to be writable.
        if (!file_exists("$path/config.php")) {
            array_unshift($writable_directories, $path);
        }

        foreach ($writable_directories as $dir) {
            $this->addRequirement(
                is_writable($dir),
                "{$dir} directory must be writable",
                "Change the permissions of the \"<strong>{$dir}</strong>\" directory so that the web server can write into it."
            );
        }

        $this->addRequirement(
            file_exists("$path/.htaccess"),
            ".htaccess does not exist",
            "Make sure the <strong>.htaccess</strong> file has been uploaded, sometimes hidden files are not uploaded using FTP/SFTP.");

        if (function_exists('opcache_invalidate') && ini_get('opcache.enable')) {
            $this->addPhpIniRequirement('opcache.load_comments', true, true);
            $this->addPhpIniRequirement('opcache.save_comments', true, true);
        }

        /* optional recommendations follow */

        $this->addRecommendation(
            function_exists('curl_init'),
            'curl_init() should be available',
            'Install and enable the <strong>cURL</strong> extension.'
        );

        $this->addRecommendation(
            function_exists('iconv'),
            'iconv() should be available',
            'Install and enable the <strong>iconv</strong> extension.'
        );

        $this->addRecommendation(
            function_exists('utf8_decode'),
            'utf8_decode() should be available',
            'Install and enable the <strong>XML Parser</strong> extension.'
        );

        if (extension_loaded('apcu')) {
            $this->addRecommendation(
                version_compare(phpversion('apcu'), '4.0.2', '>='),
                'APCu version must be at least 4.0.2',
                'Upgrade your <strong>APCu</strong> extension (4.0.2+).'
            );
        }

        if (function_exists('apc_store') && ini_get('apc.enabled')) {
            $this->addRequirement(
                version_compare(phpversion('apc'), '3.1.13', '>='),
                'APC version must be at least 3.1.13 when using PHP 5.4',
                'Upgrade your <strong>APC</strong> extension (3.1.13+).'
            );
        }

        $accelerator = (function_exists('apc_store') && ini_get('apc.enabled'))
                        || (function_exists('eaccelerator_put') && ini_get('eaccelerator.enable'))
                        || (function_exists('opcache_invalidate') && ini_get('opcache.enable'))
                        || function_exists('xcache_set');

        $this->addRecommendation(
            $accelerator,
            'a PHP accelerator should be installed',
            'Install and enable a <strong>PHP accelerator</strong> like APC (highly recommended).'
        );

        $this->addPhpIniRecommendation('short_open_tag', false);
        $this->addPhpIniRecommendation('magic_quotes_gpc', false, true);
        $this->addPhpIniRecommendation('register_globals', false, true);
        $this->addPhpIniRecommendation('session.auto_start', false);
    }
}

return new PagekitRequirements($path);
