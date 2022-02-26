 docker build "C:\Users\slmax\Documents\Capstone\Public\scripts\docker\osu-apache-php" -t "osu-apache-php" 
    docker run -d ^
        --name "osu-local-web-server" ^
        --network "osu-local-dev-net" ^
        -p "7000:80" ^
        -v "C:\Users\slmax\Documents\Capstone\Public\src\public:/var/www/html" ^
        -v "C:\Users\slmax\Documents\Capstone\Private:/var/www" ^
        "osu-apache-php" 