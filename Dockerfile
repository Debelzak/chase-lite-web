FROM php:7.2-apache

## Enable mods
RUN mv /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled \
    && mv /etc/apache2/mods-available/headers.load /etc/apache2/mods-enabled

## PHP PDO_MYSQL Install
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo pdo_mysql

## PDO_ODBC
RUN apt-get install gnupg gnupg1 gnupg2 unixodbc unixodbc-dev -y \
    && docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
    && docker-php-ext-install pdo_odbc \
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql17 \
    && sed -i 's,^\(MinProtocol[ ]*=\).*,\1'TLSv1.0',g' /etc/ssl/openssl.cnf \
    && sed -i -E 's/(CipherString\s*=\s*DEFAULT@SECLEVEL=)2/\11/' /etc/ssl/openssl.cnf