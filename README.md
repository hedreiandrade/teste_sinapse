Teste Hedrei Andrade

Costumo criar um Middlewire com JWT para não deixar as rotas desprotegidas, porém não foi solicitado no teste.

---------------------------------------------------------------------------------------
Rodando com Docker : 
---------------------------------------------------------------------------------------

Clonar o projeto, Rode :

```
git clone https://github.com/hedreiandrade/teste_sinapse.git
```

Após clonar o projeto de algumas permissões, Rode:
```
sudo chmod 777 teste_sinapse
```

Dentro de teste_sinapse, Rode:
```
sudo chmod 777 * -R
```

```
Configure o arquivo .env com as configurações de banco:
```
```
DB_CONNECTION=pgsql
DB_HOST=postgresdb
DB_PORT=5432
DB_DATABASE=gerencia_usuarios
DB_USERNAME=root
DB_PASSWORD=123
```

Dentro do projeto teste_sinapse, Rode :
```
docker-compose up -d --build
```

Dentro do container do postgresdb criar o banco: gerencia_usuarios

Agora dentro do container(laravel13_gerencia_usuarios),
Rode: ```php artisan migrate```

Ainda dentro do container, 
Rode: ```composer update```

---------------------------------------------------------------------------------------

Para acessar a aplicação acesse : 
http://localhost:8001/ na URL

Importe as rotas do Postman que estão em teste_sinapse.postman_collection.json
na raiz do projeto