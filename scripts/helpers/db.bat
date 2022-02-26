 docker run -d ^
        --name "osu-mysql-db" ^
        --network "osu-local-dev-net" ^
        -e "MYSQL_ROOT_PASSWORD=1234" ^
        -e "MYSQL_DATABASE=osulocaldev" ^
        -p "3306:3306" ^
        -v "C:\Users\slmax\Documents\Capstone\Public\src\public\scripts\database:/scripts" ^
        "mariadb:10.3"