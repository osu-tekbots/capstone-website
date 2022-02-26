docker run -d ^
        --name "osu-mysql-admin" ^
        --network "osu-local-dev-net" ^
        --link "osu-mysql-db:db" ^
        -p "5000:80" ^
        "phpmyadmin/phpmyadmin"