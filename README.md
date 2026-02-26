# account-management-api
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

Sobre
-----------------------------------------------
Projeto de gerenciamento de transações.


Configurando o projeto
-----------------------------------------------

O projeto utiliza diversas bibliotecas e ferramentas para seu funcionamento adequado. Algumas das principais dependências são:

-   Docker: Plataforma para criação e execução de contêineres. O Docker permite que as aplicações sejam executadas em um ambiente isolado, eliminando a dependência de configurações específicas do host. Mais informações podem ser encontradas em <https://www.docker.com/>.

### Dependencias do projeto

Essas bibliotecas e pacotes são essenciais para o projeto, pois fornecem funcionalidades importantes que devenmos dominar. Eles incluem o PHP, o framework Laravel, pacotes para autenticação, gerenciamento de permissões, auditoria, pesquisa de dados nos endpoints via query string, entre outros.

-   Laravel: <https://laravel.com/>
-   Bref: <https://bref.sh/>
-   PHPUnit: <https://phpunit.de/>
-   Spatie packages:
    -   Laravel Data: <https://github.com/spatie/laravel-data>
    -   Laravel HTTP Logger: <https://github.com/spatie/laravel-http-logger>
    -   Laravel Permission: <https://github.com/spatie/laravel-permission>

Executando o projeto
-----------------------------------------------
Com o Docker Compose, você pode facilmente configurar um ambiente de desenvolvimento local para o projeto com todas as suas dependências, sem precisar instalar nada além do docker. Ele consiste em um ambiente PHP / Laravel com uma API e um banco de dados MySQL. Além  de serviços de cache Redis e um contêiner pra gerenciamento de dependências com `composer` e outro para comandos do `artisan`.

### Requisitos

Para utilizar este Docker Compose, você precisará ter instalado o Docker e o Docker Compose em sua máquina seguindo as intruções da documentação.

### Executando comandos com Docker Compose

Execute o seguinte comando para criar os contêineres:

```bash
docker compose up
```

Caso haja alguma alteração, execute o seguinte comando para recriar os contêineres:

```bash
docker compose down && docker compose build
```

1.  Aguarde até que todos os contêineres estejam em execução e os logs mostrem que a API está pronta para ser acessada.

## Comandos de desenvolvimento
> Comandos que criam arquivos no docker precisam rodar como root
```bash
composer install
```
```bash
php artisan key:generate
```
```bash
php artisan migrate --seed
```
```bash
php artisan db:seed --class=DemoSeeder
```

### Executar a fila no redis
```bash
 php artisan queue:work redis
```
### Caso efrente problemas com login
```bash
php artisan passport:keys
```

### Lint Tools
Checar erros de linting:
```bash
--root ./vendor/bin/phpcs --standard=phpcs.xml -n app tests config database routes
```
Consertar erros de linting:
```bash
--root ./vendor/bin/phpcbf --standard=phpcs.xml -n app tests config database routes
```

### Remoção de cache
```bash
sudo chown -R ${USER}:${USER} ./vendor
sudo chown -R ${USER}:${USER} ./.tmp

# opcional
rm -rf vendor
rm -rf .tmp

docker compose build --no-cache
```

### Integrando o PHPStorm para a execução dos testes
Para facilitar a execução de testes com PHPUnit em um ambiente Docker Compose utilizando o PHPStorm, siga os passos abaixo:
1.  Acesse o menu "File -> Settings -> PHP -> Test".
2.  Clique no botão "+" para adicionar um novo interpretador remoto.
3.  Abra as opções de interpretação remota clicando no símbolo "...".
4.  Clique no botão "+" novamente e escolha a opção "From Docker".
5.  Selecione "Docker Compose" e escolha o serviço "CLI" no menu suspenso.
6.  Confirme as opções selecionadas e salve as configurações.
7.  No canto superior direito, próximo às opções "Run" e "Debug", clique no menu suspenso e escolha "Edit Configurations".
8.  Selecione "PHP Unit" e altere o interpretador para "CLI".

Com essas configurações, é possível executar os testes utilizando o PHPUnit no Docker Compose diretamente pelo PHPStorm, facilitando o processo de desenvolvimento e garantindo a qualidade do código. É importante lembrar que esses passos podem variar dependendo da versão do PHPStorm utilizada. Caso encontre alguma dificuldade, consulte a documentação do Docker ou do PHPStorm para obter mais informações.
