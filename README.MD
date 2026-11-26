# Lanchonete

## Requisitos
- PHP 8.2+
- Composer
- MySQL

## Como rodar
1. Copie `.env.example` para `.env` e ajuste as variáveis `DB_*`.
2. Execute `composer install`.
3. Execute `php artisan key:generate`.
4. Execute `php artisan migrate --seed`.
5. Execute `php artisan serve`.

A aplicação ficará disponível em `http://127.0.0.1:8000`.

## API de produtos

A autenticação usa Laravel Sanctum com bearer token.

- `GET /api/produtos`: lista produtos paginados.
- `GET /api/produtos/{id}`: consulta um produto.
- `POST /api/produtos`: cria um produto (gerente).
- `PATCH /api/produtos/{id}`: atualiza um produto (gerente).
- `DELETE /api/produtos/{id}`: exclui um produto (gerente).

Filtros disponíveis em `GET /api/produtos`:

- `q`: busca pelo nome.
- `categoria_id`: filtra por categoria.
- `preco_min`: preço mínimo numérico.
- `preco_max`: preço máximo numérico.
- `ativo`: filtra por `true` ou `false`.
- `sort`: `nome`, `preco` ou `created_at`.
- `dir`: `asc` ou `desc`.
- `page`: página da resposta paginada.

Exemplo:

```text
GET /api/produtos?preco_min=10&preco_max=50&ativo=true&sort=preco&dir=asc
```

## Respostas JSON

Os endpoints de criação, atualização e exclusão retornam:

```json
{
  "message": "Produto atualizado com sucesso.",
  "data": {}
}
```

Erros de validação retornam HTTP 422 no formato:

```json
{
  "message": "Os dados enviados são inválidos.",
  "data": null,
  "errors": {
    "preco_min": ["O campo preco min deve ser um número."]
  }
}
```

## Documentação

- Especificação OpenAPI: `routes/docs/openapi.yaml`
- Export da coleção Postman: `docs/postman/lanchonete-api.postman_collection.json`

Importe a coleção no Postman e configure as variáveis `baseUrl` e `token`. O token pode ser obtido em `POST /api/login`.