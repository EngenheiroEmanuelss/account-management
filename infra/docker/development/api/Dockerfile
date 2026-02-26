FROM --platform=linux/amd64 bref/php-83-fpm-dev:2

ARG GITHUB_TOKEN

ENV TZ=America/Sao_Paulo
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN yum -y install git && yum clean all

COPY --from=composer:2.5.8 /usr/bin/composer /usr/local/bin/composer

RUN if [ -n "$GITHUB_TOKEN" ]; then \
      composer config --global --auth github-oauth.github.com "$GITHUB_TOKEN"; \
    fi

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone

WORKDIR /var/task
