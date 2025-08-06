# DNA Backend

## Development server

Asegurarse de tener el archivo .env correctamente

Para comenzar el server local, en la terminal:

```bash
php artisan serve
```

## Endpoints

### POST `/api/mutation`

Detecta si la secuencia de ADN tiene mutación

**Request Body:**

```json
{
    "dna": ["ATGCGA", "CAGTGC", "TTATGT", "AGAAGG", "CCCCTA", "TCACTG"]
}
```

-   Array de strings. Solo los caracteres "A,T,C,G" son aceptados

**Success Response:**

```
HTTP 200-OK
```

### GET `/api/stats`

Estadisticas de las secuencias de ADN

**Response Body:**

```json
{
    "count_mutations": 40,
    "count_no_mutation": 100,
    "ratio": 0.4
}
```

-   count_mutations: Total de secuencias con mutación,
-   count_no_mutation: Total de secuencias sin mutación,
-   ratio: Proporción total de las secuencias

### GET `/api/list`

Últimas 10 secuencias analizadas

**Response Body:**

```json
{
    "data": [
        {
            "id": 145,
            "dna": ["ATGCGA", "CAGTGC", "TTATGT", "AGAAGG", "CCCCTA", "TCACTG"],
            "has_mutation": true,
            "created_at": "2025-08-06T09:12:55-07:00"
        },
        {
            "id": 144,
            "dna": ["ATGCGA", "CAGTGC", "TTATTT", "AGAAGG", "CCCCTA", "TCACTG"],
            "has_mutation": false,
            "created_at": "2025-08-06T09:10:11-07:00"
        }
    ]
}
```
