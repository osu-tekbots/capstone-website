#!/bin/bash

# Find the root folder of the git repo
SOURCE=$(realpath $(dirname "${BASH_SOURCE[0]}"))
ROOT_FOLDER=$(git -C ${SOURCE} rev-parse --show-toplevel);

# Source our variables and functions
source "${ROOT_FOLDER}/scripts/dev-vars.sh"
source "${ROOT_FOLDER}/scripts/helpers/functions.sh"

# Make sure the private folder exists
mkdir -p "${PRIVATE_FOLDER}";

# Make sure that none of the docker containers are running
dev_destroy_containers; # helpers/functions.sh

# Setup the config files
envsubst < "${ROOT_FOLDER}/scripts/config/config.ini.dev" > "${PUBLIC_FOLDER}/config.ini";
envsubst < "${ROOT_FOLDER}/scripts/config/database.ini.dev" > "${PRIVATE_FOLDER}/database.ini";
cp -f "${ROOT_FOLDER}/scripts/config/auth.ini.dev" "${PRIVATE_FOLDER}/auth.ini";
cp -f "${ROOT_FOLDER}/scripts/config/out.log.dev" "${PRIVATE_FOLDER}/out.log";
chmod a+w "${PRIVATE_FOLDER}/out.log";

# Rebuild/Start all of the docker containers
dev_setup_containers; # helpers/functions.sh
