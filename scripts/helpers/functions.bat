@ECHO OFF
call:%~1
GOTO EXIT

:dev_destroy_containers
    docker stop "%MYSQL_CONTAINER_NAME%" "%PHP_MY_ADMIN_CONTAINER_NAME%"  "%APACHE_PHP_CONTAINER_NAME%" >nul 2>&1
    docker rm "%MYSQL_CONTAINER_NAME%" "%PHP_MY_ADMIN_CONTAINER_NAME%"  "%APACHE_PHP_CONTAINER_NAME%" >nul 2>&1
    docker network rm "%NETWORK_NAME%" >nul 2>&1
GOTO EOF


:dev_setup_config_files
    rem Set database.ini
    del "%PUBLIC_FOLDER%\database.ini" >nul 2>&1
    (
        echo host = %MYSQL_CONTAINER_NAME%
        echo user = %DB_USERNAME%
        echo password = %DB_PASSWORD%
        echo db_name = %DB_NAME%
    )> "%PUBLIC_FOLDER%\database.ini"


    rem Set config.ini
    del "%PUBLIC_FOLDER%\config.ini" >nul 2>&1
    (
        echo private_files = /var/www
        echo [server]
        echo display_errors = yes
        echo display_errors_severity = all
        echo auth_providers_config_file = auth.ini
        echo [client]
        echo base_url = http://localhost:%APACHE_PHP_LOCAL_PORT%/
        echo [logger]
        echo log_file = out.log
        echo level = info
        echo [database]
        echo config_file = database.ini
    )> "%PUBLIC_FOLDER%\config.ini"

    mkdir "%PRIVATE_FOLDER%" >nul 2>&1

    rem Set auth.ini
    del "%PRIVATE_FOLDER%\auth.ini" >nul 2>&1
    echo "" > "%PRIVATE_FOLDER%\auth.ini"

    rem Set out.log
    del"%PRIVATE_FOLDER%\out.log" >nul 2>&1
    echo "" > "%PRIVATE_FOLDER%\out.log"
GOTO EOF


:dev_create_containers

    docker network create --driver bridge "%NETWORK_NAME%" >nul 2>&1

    echo "Creating and starting MySQL server container..."
    docker run -d ^
        --name "%MYSQL_CONTAINER_NAME%" ^
        --network "%NETWORK_NAME%" ^
        -e "MYSQL_ROOT_PASSWORD=%DB_PASSWORD%" ^
        -e "MYSQL_DATABASE=%DB_NAME%" ^
        -p "%MYSQL_LOCAL_PORT%:3306" ^
        -v "%PUBLIC_FOLDER%\scripts\database:/scripts" ^
        "%MYSQL_IMAGE%" >nul 2>&1
        
    echo "Creating and starting phpMyAdmin container..."
    docker run -d ^
        --name "%PHP_MY_ADMIN_CONTAINER_NAME%" ^
        --network "%NETWORK_NAME%" ^
        --link "%MYSQL_CONTAINER_NAME%:db" ^
        -p "%PHP_MY_ADMIN_LOCAL_PORT%:80" ^
        "%PHP_MY_ADMIN_IMAGE%" >nul 2>&1

    echo "Building and starting custom Apache PHP server for OSU website development..."
    docker build "%APACHE_DOCKERFILE_FOLDER%" -t "%APACHE_PHP_IMAGE%" >nul 2>&1
    docker run -d ^
        --name "%APACHE_PHP_CONTAINER_NAME%" ^
        --network "%NETWORK_NAME%" ^
        -p "%APACHE_PHP_LOCAL_PORT%:80" ^
        -v "%PUBLIC_FOLDER%:/var/www/html" ^
        -v "%PRIVATE_FOLDER%:/var/www" ^
        "%APACHE_PHP_IMAGE%" >nul 2>&1

    echo "";
    echo "Local OSU website development environment setup complete."
    echo "3 docker containers were started:";
    echo "    - %MYSQL_CONTAINER_NAME%";
    echo "    - %PHP_MY_ADMIN_CONTAINER_NAME%";
    echo "    - %APACHE_PHP_CONTAINER_NAME%";
    echo ""
    echo "All containers are part of the %NETWORK_NAME% docker bridge network."
    echo ""
    echo "The script has created a MySQL database server with the following credentials:"
    echo "    host: %MYSQL_CONTAINER_NAME%"
    echo "    user: root"
    echo "    pass: %DB_PASSWORD%"
    echo "    name: %DB_NAME%"
    echo ""
    echo "phpMyAdmin is available at http://localhost:%PHP_MY_ADMIN_LOCAL_PORT%"
    echo ""
    echo "The Apache PHP server serving content for the website is listening at"
    echo "http://localhost:%APACHE_PHP_LOCAL_PORT%"


GOTO EOF

:dev_setup_containers

GOTO EOF

:EXIT
EXIT /b

:EOF