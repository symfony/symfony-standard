<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param Boolean      $fulfilled     Whether the requirement is fulfilled
     * @param string       $testMessage   The message for testing the requirement
     * @param string       $helpHtml      The help text formatted in HTML for resolving the problem
     * @param string|null  $helpText      The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     * @param Boolean      $optional      Whether this is only an optional recommendation not a mandatory requirement
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
     * @param string            $cfgName            The configuration name used for ini_get()
     * @param Boolean|callback  $evaluation         Either a Boolean indicating whether the configuration should evaluate to true or false,
                                                    or a callback function receiving the configuration value as parameter to determine the fulfillment of the requirement
     * @param Boolean           $approveCfgAbsence  If true the Requirement will be fulfilled even if the configuration option does not exist, i.e. ini_get() returns false.
                                                    This is helpful for abandoned configs in later PHP versions or configs of an optional extension, like Suhosin.
                                                    Example: You require a config to be true but PHP later removes this config and defaults it to true internally.
     * @param string            $testMessage        The message for testing the requirement (when null and $evaluation is a Boolean a default message is derived)
     * @param string            $helpHtml           The help text formatted in HTML for resolving the problem (when null and $evaluation is a Boolean a default help is derived)
     * @param string|null       $helpText           The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     * @param Boolean           $optional           Whether this is only an optional recommendation not a mandatory requirement
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
 * Users of PHP 5.2 should be able to run the requirements checks.
 * This is why the class must be compatible with PHP 5.2
 * (e.g. not using namespaces and closures).
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
     * @param Boolean      $fulfilled     Whether the requirement is fulfilled
     * @param string       $testMessage   The message for testing the requirement
     * @param string       $helpHtml      The help text formatted in HTML for resolving the problem
     * @param string|null  $helpText      The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addRequirement($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Adds an optional recommendation.
     *
     * @param Boolean      $fulfilled     Whether the recommendation is fulfilled
     * @param string       $testMessage   The message for testing the recommendation
     * @param string       $helpHtml      The help text formatted in HTML for resolving the problem
     * @param string|null  $helpText      The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addRecommendation($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, true));
    }

    /**
     * Adds a mandatory requirement in form of a php.ini configuration.
     *
     * @param string            $cfgName            The configuration name used for ini_get()
     * @param Boolean|callback  $evaluation         Either a Boolean indicating whether the configuration should evaluate to true or false,
                                                    or a callback function receiving the configuration value as parameter to determine the fulfillment of the requirement
     * @param Boolean           $approveCfgAbsence  If true the Requirement will be fulfilled even if the configuration option does not exist, i.e. ini_get() returns false.
                                                    This is helpful for abandoned configs in later PHP versions or configs of an optional extension, like Suhosin.
                                                    Example: You require a config to be true but PHP later removes this config and defaults it to true internally.
     * @param string            $testMessage        The message for testing the requirement (when null and $evaluation is a Boolean a default message is derived)
     * @param string            $helpHtml           The help text formatted in HTML for resolving the problem (when null and $evaluation is a Boolean a default help is derived)
     * @param string|null       $helpText           The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addPhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence = false, $testMessage = null, $helpHtml = null, $helpText = null)
    {
        $this->add(new PhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Adds an optional recommendation in form of a php.ini configuration.
     *
     * @param string            $cfgName            The configuration name used for ini_get()
     * @param Boolean|callback  $evaluation         Either a Boolean indicating whether the configuration should evaluate to true or false,
                                                    or a callback function receiving the configuration value as parameter to determine the fulfillment of the requirement
     * @param Boolean           $approveCfgAbsence  If true the Requirement will be fulfilled even if the configuration option does not exist, i.e. ini_get() returns false.
                                                    This is helpful for abandoned configs in later PHP versions or configs of an optional extension, like Suhosin.
                                                    Example: You require a config to be true but PHP later removes this config and defaults it to true internally.
     * @param string            $testMessage        The message for testing the requirement (when null and $evaluation is a Boolean a default message is derived)
     * @param string            $helpHtml           The help text formatted in HTML for resolving the problem (when null and $evaluation is a Boolean a default help is derived)
     * @param string|null       $helpText           The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
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
 * This class specifies all requirements and optional recommendations that
 * are necessary to run the Symfony Standard Edition.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class SymfonyRequirements extends RequirementCollection
{
    const REQUIRED_PHP_VERSION = '5.3.3';

    /**
     * Constructor that initializes the requirements.
     */
    public function __construct()
    {
        /* mandatory requirements follow */

        $installedPhpVersion = phpversion();

        $this->addRequirement(
            version_compare($installedPhpVersion, self::REQUIRED_PHP_VERSION, '>='),
            sprintf('PHP version must be at least %s (%s installed)', self::REQUIRED_PHP_VERSION, $installedPhpVersion),
            sprintf('You are running PHP version "<strong>%s</strong>", but Symfony needs at least PHP "<strong>%s</strong>" to run.
                Before using Symfony, upgrade your PHP installation, preferably to the latest version.',
                $installedPhpVersion, self::REQUIRED_PHP_VERSION),
            sprintf('Install PHP %s or newer (installed version is %s)', self::REQUIRED_PHP_VERSION, $installedPhpVersion)
        );

        $this->addRequirement(
            is_dir(__DIR__.'/../vendor/symfony'),
            'Vendor libraries must be installed',
            'Vendor libraries are missing. Install composer following instructions from <a href="http://getcomposer.org/">http://getcomposer.org/</a>. ' .
                'Then run "<strong>php composer.phar install</strong>" to install them.'
        );

        $baseDir = basename(__DIR__);
        $this->addRequirement(
            is_writable(__DIR__.'/cache'),
            "$baseDir/cache/ directory must be writable",
            "Change the permissions of the \"<strong>$baseDir/cache/</strong>\" directory so that the web server can write into it."
        );

        $this->addRequirement(
            is_writable(__DIR__.'/logs'),
            "$baseDir/logs/ directory must be writable",
            "Change the permissions of the \"<strong>$baseDir/logs/</strong>\" directory so that the web server can write into it."
        );

        $this->addPhpIniRequirement(
            'date.timezone', true, false,
            'date.timezone setting must be set',
            'Set the "<strong>date.timezone</strong>" setting in php.ini<a href="#phpini">*</a> (like Europe/Paris).'
        );

        $this->addRequirement(
            function_exists('json_encode'),
            'json_encode() must be available',
            'Install and enable the <strong>JSON</strong> extension.'
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
            !(function_exists('apc_store') && ini_get('apc.enabled')) || version_compare(phpversion('apc'), '3.0.17', '>='),
            'APC version must be at least 3.0.17',
            'Upgrade your <strong>APC</strong> extension (3.0.17+)'
        );

        $this->addPhpIniRequirement('detect_unicode', false);

        $this->addPhpIniRequirement(
            'suhosin.executor.include.whitelist',
            create_function('$cfgValue', 'return false !== stripos($cfgValue, "phar");'),
            true,
            'suhosin.executor.include.whitelist must be configured correctly in php.ini',
            'Add "<strong>phar</strong>" to <strong>suhosin.executor.include.whitelist</strong> in php.ini<a href="#phpini">*</a>.'
        );

        $pcreVersion = defined('PCRE_VERSION') ? (float) PCRE_VERSION : null;

        $this->addRequirement(
            null !== $pcreVersion && $pcreVersion > 8.0,
            sprintf('PCRE extension must be available and at least 8.0 (%s installed)', $pcreVersion ? $pcreVersion : 'not'),
            'Upgrade your <strong>PCRE</strong> extension (8.0+)'
        );

        /* optional recommendations follow */

        $this->addRecommendation(
            version_compare($installedPhpVersion, '5.3.8', '>='),
            sprintf('Annotations might not work properly due to the PHP bug #55156 before PHP 5.3.8 (%s installed)', $installedPhpVersion),
            'Install PHP 5.3.8 or newer if your project uses annotations'
        );

        $this->addRecommendation(
            class_exists('DomDocument'),
            'PHP-XML module should be installed',
            'Install and enable the <strong>PHP-XML</strong> module.'
        );

        $this->addRecommendation(
            function_exists('mb_strlen'),
            'mb_strlen() should be available',
            'Install and enable the <strong>mbstring</strong> extension.'
        );

        $this->addRecommendation(
            function_exists('iconv'),
            'iconv() should be available',
            'Install and enable the <strong>iconv</strong> extension.'
        );

        $this->addRecommendation(
            function_exists('utf8_decode'),
            'utf8_decode() should be available',
            'Install and enable the <strong>XML</strong> extension.'
        );

        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->addRecommendation(
                function_exists('posix_isatty'),
                'posix_isatty() should be available',
                'Install and enable the <strong>php_posix</strong> extension (used to colorize the CLI output).'
            );
        }

        $this->addRecommendation(
            class_exists('Locale'),
            'intl extension should be available',
            'Install and enable the <strong>intl</strong> extension (used for validators).'
        );

        if (class_exists('Locale')) {
            if (defined('INTL_ICU_VERSION')) {
                $version = INTL_ICU_VERSION;
            } else {
                $reflector = new ReflectionExtension('intl');

                ob_start();
                $reflector->info();
                $output = strip_tags(ob_get_clean());

                preg_match('/^ICU version +(?:=> )?(.*)$/m', $output, $matches);
                $version = $matches[1];
            }

            $this->addRecommendation(
                version_compare($version, '4.0', '>='),
                'intl ICU version should be at least 4+',
                'Upgrade your <strong>intl</strong> extension with a newer ICU version (4+).'
            );
        }

        $accelerator =
            (function_exists('apc_store') && ini_get('apc.enabled'))
            ||
            function_exists('eaccelerator_put') && ini_get('eaccelerator.enable')
            ||
            function_exists('xcache_set')
        ;

        $this->addRecommendation(
            $accelerator,
            'a PHP accelerator should be installed',
            'Install and enable a <strong>PHP accelerator</strong> like APC (highly recommended).'
        );

        $this->addPhpIniRecommendation('short_open_tag', false);

        $this->addPhpIniRecommendation('magic_quotes_gpc', false, true);

        $this->addPhpIniRecommendation('register_globals', false, true);

        $this->addPhpIniRecommendation('session.auto_start', false);

        $this->addRecommendation(
            class_exists('PDO'),
            'PDO should be installed',
            'Install <strong>PDO</strong> (mandatory for Doctrine).'
        );

        if (class_exists('PDO')) {
            $drivers = PDO::getAvailableDrivers();
            $this->addRecommendation(
                count($drivers),
                sprintf('PDO should have some drivers installed (currently available: %s)', count($drivers) ? implode(', ', $drivers) : 'none'),
                'Install <strong>PDO drivers</strong> (mandatory for Doctrine).'
            );
        }
    }
}
