# Wrapper to run the MailHog connectivity check inside the app container
docker-compose exec app php /var/www/html/scripts/check_mailhog.php
