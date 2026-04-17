# Controller API

Tutti gli endpoint restituiscono risposte JSON. Base URL: `/api`

## CategoryController

**File**: `src/Controller/CategoryController.php`

Gestisce le operazioni CRUD per le categorie.

### Endpoints

#### POST `/api/categories` - Crea Categoria
- **Body JSON**: `{"name": "Nome Categoria"}`
- **Risposta**: `{"message": "Categoria creata con successo!", "id": 1}`
- **Errori**: 400 se manca il nome

#### GET `/api/categories` - Lista Categorie (con paginazione)
- **Query params**: `page` (default 1), `limit` (default 5)
- **Risposta**:
```json
{
  "data": [{"id": 1, "name": "Alimentari"}],
  "meta": {
    "current_page": 1,
    "items_per_page": 5,
    "total_items": 4,
    "total_pages": 1
  }
}
```

#### GET `/api/categories/{id}` - Mostra Categoria Singola
- **Risposta**: `{"id": 1, "name": "Alimentari"}`
- **Errori**: 404 se non trovata

#### PUT/PATCH `/api/categories/{id}` - Aggiorna Categoria
- **Body JSON**: `{"name": "Nuovo Nome"}` (solo campi da aggiornare)
- **Risposta**: `{"message": "Categoria aggiornata con successo!"}`
- **Errori**: 404 se non trovata

#### DELETE `/api/categories/{id}` - Elimina Categoria
- **Risposta**: `{"message": "Categoria eliminata!"}`
- **Errori**: 404 se non trovata

## ProductController

**File**: `src/Controller/ProductController.php`

Gestisce le operazioni CRUD per i prodotti.

### Endpoints

#### POST `/api/products` - Crea Prodotto
- **Body JSON opzioni**:
  - Base: `{"name": "Prodotto", "price": 99.99}`
  - Con categoria esistente: `{"name": "Prodotto", "price": 99.99, "category_id": 1}`
  - Con nuova categoria: `{"name": "Prodotto", "price": 99.99, "category": {"name": "Nuova Cat"}}`
- **Validazione**: Nome obbligatorio, prezzo > 0
- **Risposta**: `{"message": "Prodotto creato!", "id": 1}`
- **Errori**: 400 dati mancanti/invalidi, 404 categoria non trovata

#### GET `/api/products` - Lista Prodotti (con paginazione)
- **Query params**: `page` (default 1), `limit` (default 10)
- **Risposta**:
```json
{
  "items": [
    {
      "id": 1,
      "name": "Laptop Pro 16",
      "price": 1250.00,
      "category": "Informatica"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 1,
    "total_items": 9
  }
}
```

#### GET `/api/products/{id}` - Mostra Prodotto Singolo
- **Risposta**: `{"id": 1, "name": "Laptop", "price": 1250.00, "category": "Informatica"}`
- **Errori**: 404 se non trovato

#### PUT/PATCH `/api/products/{id}` - Aggiorna Prodotto
- **Body JSON**: `{"name": "Nuovo Nome", "price": 999.99, "category_id": 2}`
- **Risposta**: `{"message": "Prodotto aggiornato con successo!"}`
- **Errori**: 404 se prodotto/categoria non trovati

#### DELETE `/api/products/{id}` - Elimina Prodotto
- **Risposta**: `{"message": "Prodotto eliminato!"}`
- **Errori**: 404 se non trovato

## Note Generali
- Tutti gli endpoint usano JSON per request/response
- Paginazione implementata con page/limit
- Errori restituiti con codici HTTP appropriati
- Validazione input sui campi obbligatori</content>
<parameter name="filePath">c:\Xiao\PCTO\progetto_fsl_qiu\docs\Controllers.md