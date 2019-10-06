FROM debian:buster-slim

ENV DEBIAN_FRONTEND=noninteractive \
    XDEBUG_ENABLED=0

# Install PHP packages from Ondrej Sury repository
RUN echo 'APT::Install-Recommends "0" ; APT::Install-Suggests "0" ;' > /etc/apt/apt.conf.d/01-no-recommended && \
    echo 'path-exclude=/usr/share/doc/*' > /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/groff/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/info/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/linda/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/lintian/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/locale/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/man/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    apt-get update && \
    apt-get --no-install-recommends --no-install-suggests --yes --quiet install \
        apt-transport-https \
        ca-certificates \
        curl \
        gpg \
        gpg-agent && \
    apt-get clean && \
    apt-get --yes autoremove --purge && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    echo 'deb https://packages.sury.org/php/ buster main' > /etc/apt/sources.list.d/sury.list && \
    curl https://packages.sury.org/php/apt.gpg -O && apt-key add apt.gpg && rm apt.gpg && \
    apt-get update && \
    apt-get --yes install \
        php7.2-apcu \
        php7.2-cli \
        php7.2-fpm \
        php7.2-intl \
        php7.2-mbstring \
        php7.2-mysql \
        php7.2-opcache \
        php7.2-pdo \
        php7.2-xdebug \
        php7.2-xml \
        php7.2-zip && \
    apt-get clean && \
    apt-get --yes autoremove --purge && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    ln -s /usr/sbin/php-fpm7.2 /usr/local/sbin/php-fpm && \
    mkdir -p /run/php && \
    phpdismod xdebug && \
    mkdir /etc/php/7.2/enable-xdebug && \
    ln -s /etc/php/7.2/mods-available/xdebug.ini /etc/php/7.2/enable-xdebug/xdebug.ini

# Configure PHP
COPY files/khatovar.ini /etc/php/7.2/cli/conf.d/99-khatovar.ini
COPY files/khatovar.ini /etc/php/7.2/fpm/conf.d/99-khatovar.ini
COPY files/fpm.conf /etc/php/7.2/fpm/pool.d/zzz-khatovar.conf

# Configure XDEBUG and make XDEBUG activable at container start
COPY files/xdebug.ini /etc/php/7.2/cli/conf.d/99-xdebug.ini
COPY files/xdebug.ini /etc/php/7.2/fpm/conf.d/99-xdebug.ini
COPY files/docker-php-entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-php-entrypoint

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# Create volumes
RUN mkdir -p /srv/khatovar/var && chmod -R 777 /srv/khatovar

# Expose volumes
VOLUME /srv/khatovar/var
VOLUME /srv/khatovar

# Expose port for PHP internal server
EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/docker-php-entrypoint"]
