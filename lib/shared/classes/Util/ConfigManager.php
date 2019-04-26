<?php
namespace Util;

/**
 * Configuration manager for handling basic site-wide configuration. 
 * 
 * When an instance is constructed, it will look for a `site.ini` file in the provided configuration directory. This
 * file should have a `mode` property that is set to the INI file which will be loaded as the configuration for
 * the site. There are three conventional modes:
 * - `production` : settings for the production server
 * - `development` : settings for the staging/development area running on OSU ENGR servers
 * - `local` : settings for development locally by running an Apache PHP server, either on bare metal or in a container
 */
class ConfigManager {
    private $configDir = null;
    private $config = null;
    private $mode = null;

    private $shouldDisplayErrors = null;
    private $displayErrorSeverity = null;
    private $databaseConfig = null;
    private $authProviderConfig = null;

    public function __construct($configDir) {
        $this->configDir = $configDir;

        $siteConfig = $this->loadIni(join('/', array($configDir, 'site.ini')));

        // There is a config mode specified in the site.ini file. Load the configuration
        // associated with the mode for the site
        $this->mode = $siteConfig['mode'];
        $this->config = array_merge($siteConfig, $this->loadIni(join('/', array($configDir, $this->mode . '.ini'))));

        // Handle the default configurations
        $this->setShouldDisplayErrors($this->config['server']['display_errors']);
        $this->setDisplayErrorSeverity($this->config['server']['display_errors_severity']);
        $this->databaseConfig = $this->loadIni(join('/', array($this->getPrivateFilesDirectory(), 
            $this->config['database']['config_file'])));
        $this->authProviderConfig = $this->loadIni(join('/', array($this->getPrivateFilesDirectory(), 
            $this->config['server']['auth_providers_config_file'])));
    }

    /**
     * Loads an INI file with sections.
     *
     * @param string $file the file location of the INI file to read
     * @return mixed[]
     */
    public function loadIni($file) {
        return parse_ini_file($file, true);
    }

    /**
     * Fetches the current server mode.
     *
     * @return string
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * Fetches the location of the private files for the server, such as DB and other sensitive configuration.
     *
     * @return string
     */
    public function getPrivateFilesDirectory() {
        return $this->config['private_files'];
    }

    /**
     * Configures the server to display or hide errors.
     * 
     * This function uses the `ini_set()` PHP function internally.
     *
     * @param boolean $should whether or not the server should print errors to the output buffer
     * @return void
     */
    public function setShouldDisplayErrors($should) {
        $this->shouldDisplayErrors = $should;
        if ($should) {
            ini_set('display_errors', 1);
        }
    }

    /**
     * Indicates whether or not the server is configured to display errors.
     *
     * @return boolean
     */
    public function shouldDisplayErrors() {
        return $this->shouldDisplayErrors;
    }

    /**
     * Indicates the severity level that determines whether errors are displayed
     * 
     * Possible values are `notice`, `warning`, and `all`.
     *
     * @return string
     */
    public function getDisplayErrorSeverity() {
        return $this->displayErrorSeverity;
    }

    /**
     * Sets the severity level that determines what level errors are displayed at.
     * 
     * Possible values are `notice`, `warning`, and `all`.
     *
     * @param string $sev the severity level
     * @return void
     */
    public function setDisplayErrorSeverity($sev) {
        $this->displayErrorSeverity = $sev;
        switch ($sev) {
        case 'notice':
            $sev = E_NOTICE;
            break;
        case 'warning':
            $sev = E_WARNING;
            break;
        case 'all':
            $sev = E_ALL;
            break;
        default:
            $this->displayErrorSeverity = 'warning';
            $sev = E_WARNING;
            break;
        }
        error_reporting($sev);
    }

    /**
     * Fetches the base URL to use for the website.
     * 
     * This base URL can be used to set the `<base>` HTML tag value and create a relative root from which all
     * links in the website can be resolved.
     *
     * @return string
     */
    public function getBaseUrl() {
        return $this->config['client']['base_url'];
    }

    /**
     * Fetches the configuration used by the server to communicate with the database.
     * 
     * The resulting array will contain the following fields:
     * - `host` : the host IP address or URL for the database server
     * - `user` : the username the server will authenticate with
     * - `password` : the password used to authenticate the user
     * - `db_name` : the name of the database to connect with
     *
     * @return string[]
     */
    public function getDatabaseConfig() {
        return $this->databaseConfig;
    }

    /**
     * Fetches the client IDs and secrets for the configured auth providers.
     * 
     * The format of the configuration will be a two-dimensional array with the first dimension being the name
     * of the provider and the second dimension containing the client ID and secret for the provider. The client
     * ID will be available as the `client_id` key and the secret as the `secret` key.
     *
     * @return mixed[]
     */
    public function getAuthProviderConfig() {
        return $this->authProviderConfig;
    }

    /**
     * Fetches the full path to the log file used for log output.
     *
     * @return string the path to the log file
     */
    public function getLogFilePath() {
        return $this->getPrivateFilesDirectory() . '/' . $this->config['logger']['log_file'];
    }

    /**
     * Fetches the level of the logger from configuration.
     *
     * @return string the level of the logger
     */
    public function getLogLevel() {
        return $this->config['logger']['level'];
    }

    /**
     * Fetches the raw configuration object loaded by the manager.
     *
     * @return mixed[]
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Fetches an optional subject tag to prefix to email subjects.
     *
     * @return string|null the email address on success, null otherwise
     */
    public function getEmailSubjectTag() {
        if(\array_key_exists('email', $this->config)) {
            return $this->config['email']['subject_tag'];
        }
        return null;
    }

    /**
     * Fetches the from address for emails sent from this server.
     *
     * @return string|boolean the email address on success, false otherwise
     */
    public function getEmailFromAddress() {
        if(\array_key_exists('email', $this->config)) {
            return $this->config['email']['from_address'];
        }
        return false;
    }

    /**
     * Fetches the admin email addresses for the server.
     *
     * @return string|boolean the email address on success, false otherwise
     */
    public function getEmailAdminAddresses() {
        if(\array_key_exists('email', $this->config)) {
            return $this->config['email']['admin_addresses'];
        }
        return false;
    }
}
