# aplicação para teste

#aplicação feita com php no back-end e vuejs no front-end

#recursos necessários para instalção:
#php 8.1
#node 18+
#Vue 3
#laravel 10
#mysql (latest)
#composer (latest)
#npm 9.*+

#dependências adicionais:
#lucascudo/laravel-pt-br-localization (para tradução da validação no laravel)
#vue-the-mask (para máscaras nos campos do vuejs)
#template oneui

#instalação
#na raiz do projeto com os recursos instalados rode os seguintes comandos:
#composer install
#php artisan key:generate
#npm install
#php artisan migrate


#nota
#deve ser configurado a conexão do banco de dados no arquivo .env da aplicação e logo em seguida roda o comando 'php artisan optimize:clear'
#deve ser inserido pela interface um cargo, centro de custo, departamento e então o usuário.
#para rodar a aplicação na raiz do projeto rode os comandos 'php artisan serve' e em seguida 'npm run dev' (para rodar o build 'npm run build') estará diponível em localhost:8000