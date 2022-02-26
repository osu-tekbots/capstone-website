#!/bin/bash

simple_print() {
    echo "${1}"
}

tab_print() {
    echo "    ${1}"
}

log_print() {
    echo "LOG: ${1}"
}
wrn_print() {
    echo "WRN: ${1}"
}
err_print() {
    echo "ERR: ${1}"
}

error_print_missing_environment_var() {
    err_print "A required environment variable '${1}' is missing!"
    err_print "Please add this variable to the dev-vars.sh variable file."
    err_print "${1}='...'"
}

dev_destroy_containers() {
    if [ -z "${MYSQL_CONTAINER_NAME}" ]; then
        error_print_missing_environment_var "MYSQL_CONTAINER_NAME";
        exit 1;
    fi
    if [ -z "${PHP_MY_ADMIN_CONTAINER_NAME}" ]; then
        error_print_missing_environment_var "PHP_MY_ADMIN_CONTAINER_NAME";
        exit 1;
    fi
    if [ -z "${APACHE_PHP_CONTAINER_NAME}" ]; then
        error_print_missing_environment_var "APACHE_PHP_CONTAINER_NAME";
        exit 1;
    fi
    if [ -z "${NETWORK_NAME}" ]; then
        error_print_missing_environment_var "NETWORK_NAME";
        exit 1;
    fi

    simple_print "";
    log_print "Stopping containers..."
    docker stop "${MYSQL_CONTAINER_NAME}" "${PHP_MY_ADMIN_CONTAINER_NAME}"  "${APACHE_PHP_CONTAINER_NAME}" > /dev/null 2>&1;

    log_print "Removing containers..."
    docker rm "${MYSQL_CONTAINER_NAME}" "${PHP_MY_ADMIN_CONTAINER_NAME}"  "${APACHE_PHP_CONTAINER_NAME}" > /dev/null 2>&1;

    log_print "Removing bridge network...";
    docker network rm "${NETWORK_NAME}" > /dev/null 2>&1;

    log_print "Done!";
}

dev_create_containers() {
    dev_destroy_containers;

    simple_print "";
    log_print "Creating bridge network for development containers...";
    docker network create --driver bridge "${NETWORK_NAME}" > /dev/null;

    log_print "Creating and starting MySQL server container...";
    docker run -d \
        --name "${MYSQL_CONTAINER_NAME}" \
        --network "${NETWORK_NAME}" \
        -e "MYSQL_ROOT_PASSWORD=${DB_PASSWORD}" \
        -e "MYSQL_DATABASE=${DB_NAME}" \
        -p "${MYSQL_LOCAL_PORT}:3306" \
        -v "${PUBLIC_FOLDER}/scripts/database:/scripts" \
        "${MYSQL_IMAGE}" > /dev/null;
        
    log_print "Creating and starting phpMyAdmin container...";
    docker run -d \
        --name "${PHP_MY_ADMIN_CONTAINER_NAME}" \
        --network "${NETWORK_NAME}" \
        --link "${MYSQL_CONTAINER_NAME}:db" \
        -p "${PHP_MY_ADMIN_LOCAL_PORT}:80" \
        "${PHP_MY_ADMIN_IMAGE}" > /dev/null;

    log_print "Building custom Apache PHP server for OSU website development...";
    docker build "${APACHE_DOCKERFILE_FOLDER}" -t "${APACHE_PHP_IMAGE}" > /dev/null;
    log_print "Creating and starting custom Apache PHP server for OSU website development...";
    docker run -d \
        --name "${APACHE_PHP_CONTAINER_NAME}" \
        --network "${NETWORK_NAME}" \
        -p "${APACHE_PHP_LOCAL_PORT}:80" \
        -v "${PUBLIC_FOLDER}:/var/www/html" \
        -v "${PRIVATE_FOLDER}:/var/www" \
        "${APACHE_PHP_IMAGE}" > /dev/null;
        
    simple_print "";
    log_print "Successfully started docker containers!";
    simple_print "";
    tab_print "Local OSU website development environment setup complete.";
    tab_print "3 docker containers were started:";
    tab_print "    - ${MYSQL_CONTAINER_NAME}";
    tab_print "    - ${PHP_MY_ADMIN_CONTAINER_NAME}";
    tab_print "    - ${APACHE_PHP_CONTAINER_NAME}";
    tab_print "";
    tab_print "All containers are part of the ${NETWORK_NAME} docker bridge network.";
    tab_print "";
    tab_print "The script has created a MySQL database server with the following credentials:";
    tab_print "    host: ${MYSQL_CONTAINER_NAME}";
    tab_print "    user: root";
    tab_print "    pass: ${DB_PASSWORD}";
    tab_print "    name: ${DB_NAME}";
    tab_print;
    tab_print "phpMyAdmin is available at http://localhost:${PHP_MY_ADMIN_LOCAL_PORT}";
    tab_print "";
    tab_print "The Apache PHP server serving content for the website is listening at";
    tab_print "http://localhost:${APACHE_PHP_LOCAL_PORT}";

}

dev_setup_containers() {
    dev_create_containers;

    simple_print "";
    log_print "Waiting 10 seconds for docker containers to have their services ready..."
    sleep 10;

    # Setup Database(s) with SQL files
    log_print "Populating SQL Database...";
    docker exec -it "${MYSQL_CONTAINER_NAME}" /bin/bash -c "cd /scripts; mysql --user="${DB_USERNAME}" --password="${DB_PASSWORD}" --database="${DB_NAME}" -e 'source setup-user.sql';"
    docker exec -it "${MYSQL_CONTAINER_NAME}" /bin/bash -c "cd /scripts; mysql --user="${DB_USERNAME}" --password="${DB_PASSWORD}" --database="${DB_NAME}" -e 'source setup-capstone-project.sql';"
    docker exec -it "${MYSQL_CONTAINER_NAME}" /bin/bash -c "cd /scripts; mysql --user="${DB_USERNAME}" --password="${DB_PASSWORD}" --database="${DB_NAME}" -e 'source setup-capstone-project-log.sql';"
    docker exec -it "${MYSQL_CONTAINER_NAME}" /bin/bash -c "cd /scripts; mysql --user="${DB_USERNAME}" --password="${DB_PASSWORD}" --database="${DB_NAME}" -e 'source setup-capstone-application.sql';"
    docker exec -it "${MYSQL_CONTAINER_NAME}" /bin/bash -c "cd /scripts; mysql --user="${DB_USERNAME}" --password="${DB_PASSWORD}" --database="${DB_NAME}" -e 'source seed-capstone-enums.sql';"
    docker exec -it "${MYSQL_CONTAINER_NAME}" /bin/bash -c "cd /scripts; mysql --user="${DB_USERNAME}" --password="${DB_PASSWORD}" --database="${DB_NAME}" -e 'source seed-capstone-keywords.sql';"

    # # Setup onid user account
    log_print "Creating fake ONID accounts...";
    docker exec -it "${APACHE_PHP_CONTAINER_NAME}" /bin/bash -c "cd /var/www/html/scripts; php create-user.php 'admin' 'admin_first' 'admin_last' 3" > /dev/null;
    docker exec -it "${APACHE_PHP_CONTAINER_NAME}" /bin/bash -c "cd /var/www/html/scripts; php create-user.php 'student' 'student_first' 'student_last' 1" > /dev/null;
    docker exec -it "${APACHE_PHP_CONTAINER_NAME}" /bin/bash -c "cd /var/www/html/scripts; php create-user.php 'proposer' 'proposer_first' 'proposer_last' 2" > /dev/null;

    docker exec -it "${APACHE_PHP_CONTAINER_NAME}" /bin/bash -c "cd /var/www/html/scripts; php create-user.php '${ONID_USERNAME}' '${ONID_FIRST}' '${ONID_LAST}' ${ONID_ACCOUNT_TYPE}" > /dev/null;

    simple_print "";
    log_print "The following 4 fake ONID accounts have been created:";
    log_print "  admin, student, proposer, ${ONID_USERNAME}"
    simple_print "";
    log_print "Visit http://localhost:${APACHE_PHP_LOCAL_PORT}/masq to masquerade as a user!";
    simple_print "";
}