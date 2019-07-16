<?php
namespace Util;

/**
 * Configuration manager for handling basic site-wide configuration. 
 * 
 * When an instance is constructed, it will look for a `config.ini` file in the directory passed to the constructor.
 * That config file will be parsed and loaded into the manager. Any configuration can go into the INI file, but
 * there are a few commonly required ones that are outlined below.
 */
class ConfigManager {
    private $configDir = null;
    private $config = null;

    private $shouldDisplayErrors = null;
    private $displayErrorSeverity = null;
    private $databaseConfig = null;

    public function __construct($configDir) {
        $this->configDir = $configDir;

        // Look for a config.ini file and load the configuration. If there is no file, throw an exception
        $path = $this->join($configDir, 'config.ini');
        if (!\file_exists($path)) {
            throw new \Exception("Failed to initialize config manager: config file not found in '$configDir'");
        }
        $this->config = $this->loadIni($path);

        // Set the appropriate initial error configurations
        $shouldDisplayErrors = $this->get('server.display_errors');
        $displayErrorSev = $this->get('server.display_errors_severity');
        $this->setShouldDisplayErrors($shouldDisplayErrors);
        $this->setDisplayErrorSeverity($displayErrorSev);

        // Set the private directory location. Filepath configurations depend on the presence of this config
        // value. Don't attempt to load the dependent common configurations unless there is a private directory
        // specified
        $privateDir = $this->getPrivateFilesDirectory();
        if (!\is_null($privateDir)) {
            // Setup the initial database configurations
            $dbConfigFile = $this->get('database.config_file');
            if (!\is_null($dbConfigFile)) {
                $dbConfigPath = $this->join($privateDir, $dbConfigFile);
                $this->databaseConfig = $this->loadIni($dbConfigPath);
            }
        }
    }

    /**
     * Takes any number of arguments and joins them together to construct a filepath
     *
     * @return string the filepath resulting from the joined arguments
     */
    private function join() {
        $paths = array();
        foreach (\func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }
        return \preg_replace('#/+#', '/', \join('/', $paths));
    }

    /**
     * Loads an INI file with sections.
     *
     * @param string $file the file location of the INI file to read
     * @return mixed[]
     */
    public function loadIni($file) {
        return \parse_ini_file($file, true);
    }

    /**
     * Fetches the location of the private files for the server, such as DB and other sensitive configuration.
     *
     * @return string
     */
    public function getPrivateFilesDirectory() {
        return $this->get('private_files');
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
        \error_reporting($sev);
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
        return $this->get('client.base_url');
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
     * Fetches the full path to the log file used for log output.
     *
     * @return string|null the path to the log file if defined, null otherwise
     */
    public function getLogFilePath() {
        $privateDir = $this->getPrivateFilesDirectory();
        $logFile = $this->get('logger.log_file');
        if (!\is_null($privateDir) && !\is_null($logFile)) {
            return $this->join($privateDir, $logFile);
        }
        return null;
    }

    /**
     * Fetches the level of the logger from configuration.
     *
     * @return string|null the level of the logger if defined, null otherwise
     */
    public function getLogLevel() {
        return $this->get('logger.level');
    }

    /**
     * Fetches the configuration associated with the provided key.
     * 
     * This function allows for nested keys. The keys must be separated by a period (.). If there is not value for
     * the provided key, then null is returned.
     *
     * @param string $key the key of the value. Can be a nested key separated by periods (.)
     * @return mixed[]|null the value if it exists, null otherwise
     */
    public function get($key) {
        $parts = explode('.', $key);

        $result = null;
        if (isset($this->config[$parts[0]])) {
            $result = $this->config[$parts[0]];
        } else {
            return null;
        }
        for ($i = 1; $i < \count($parts); $i++) {
            if (isset($result[$parts[$i]])) {
                $result = $result[$parts[$i]];
            } else {
                return null;
            }
        }

        return $result;
    }
}
