SCRIPT="${0}";
SCRIPT_FOLDER="$(dirname $(realpath ${0}))";
cd "${SCRIPT_FOLDER}/..";

ROOT_FOLDER="$PWD";
SCRIPTS_FOLDER="${SCRIPT_FOLDER}/dev"
PUBLIC_FOLDER="${ROOT_FOLDER}/capstone/public"
PRIVATE_FOLDER="${ROOT_FOLDER}/capstone/private"

cd "${SCRIPTS_FOLDER}";
chmod +x ./dev-teardown.sh;
./dev-teardown.sh

cd "${ROOT_FOLDER}";


mkdir -p "${SCRIPTS_FOLDER}"
mkdir -p "${PUBLIC_FOLDER}"
mkdir -p "${PRIVATE_FOLDER}"

git clone git@github.com:DotBowder/capstone-website.git "${PUBLIC_FOLDER}"
git clone git@github.com:DotBowder/container-dev-env.git "${SCRIPTS_FOLDER}"

find "${SCRIPTS_FOLDER}" -type f -name "*.sh" -exec chmod +x {} \;

# config.ini
echo "\
; All files referenced through the configuration are relative to this private path
private_files = /var/www; directory containing private files (outside the web root)

[server]
display_errors = yes; yes|no
display_errors_severity = all; all|warning|error
auth_providers_config_file = auth.ini; auth.ini

; [email]
; subject_tag = ; optional tag to prepend all email subjects with
; from_address = ; main from address used when sending email from the server
; admin_addresses[] = ; array of email addresses of admins that need important site notifications

[client]
base_url = http://localhost:7000/; base URL used by the frontend (e.g. http://eecs.oregonstate.edu/capstone/)

[logger]
log_file = out.log; out.log, or another name pointing to the log file
level = info; trace|info|warn|error

[database]
config_file = database.ini; database.ini, or another name pointing to the database configuration file (see above for contents)
" > "${PUBLIC_FOLDER}/config.ini";

# database.ini
echo "\
host = osu-mysql-db
user = root
password = 1234
db_name = osulocaldev
" > "${PRIVATE_FOLDER}/database.ini";

# auth.ini
echo "\
" > "${PRIVATE_FOLDER}/auth.ini";

# out.log
echo "\
" > "${PRIVATE_FOLDER}/out.log";
chmod a+w "${PRIVATE_FOLDER}/out.log";



echo "Please enter 1234 as the database password.";
cd "${SCRIPTS_FOLDER}";
"${SCRIPTS_FOLDER}/dev-setup.sh" "${PUBLIC_FOLDER}" "${PRIVATE_FOLDER}";

echo "Waiting 10 seconds for docker containers to have their services ready..."
sleep 10;

# Setup Database(s) with SQL files
docker exec -it osu-mysql-db /bin/bash -c 'cd /scripts; mysql --user="root" --password="1234" --database="osulocaldev" -e "source setup-user.sql";'
docker exec -it osu-mysql-db /bin/bash -c 'cd /scripts; mysql --user="root" --password="1234" --database="osulocaldev" -e "source setup-capstone-project.sql";'
docker exec -it osu-mysql-db /bin/bash -c 'cd /scripts; mysql --user="root" --password="1234" --database="osulocaldev" -e "source setup-capstone-application.sql";'
docker exec -it osu-mysql-db /bin/bash -c 'cd /scripts; mysql --user="root" --password="1234" --database="osulocaldev" -e "source seed-capstone-enums.sql";'
docker exec -it osu-mysql-db /bin/bash -c 'cd /scripts; mysql --user="root" --password="1234" --database="osulocaldev" -e "source seed-capstone-keywords.sql";'


# Create a test user
docker exec -it osu-local-web-server /bin/bash -c 'cd /var/www/html/scripts; php create-user.php'