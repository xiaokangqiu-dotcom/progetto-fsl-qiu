# Entità del Database

## Category (Categoria)

**File**: `src/Entity/Category.php`

### Proprietà
- `id` (int): ID univoco, auto-incrementale
- `name` (string, max 255): Nome della categoria
- `products` (Collection<Product>): Prodotti associati (relazione OneToMany)

### Relazioni
- **OneToMany** con Product: Una categoria può contenere molti prodotti
- Cascade persist: Quando si salva una categoria, i prodotti vengono salvati automaticamente

### Metodi Principali
- `getId()`: Restituisce l'ID
- `getName()` / `setName(string)`: Gestione nome
- `getProducts()`: Restituisce collezione prodotti
- `addProduct(Product)` / `removeProduct(Product)`: Gestione associazione prodotti

## Product (Prodotto)

**File**: `src/Entity/Product.php`

### Proprietà
- `id` (int): ID univoco, auto-incrementale
- `name` (string, max 255): Nome del prodotto
- `price` (float): Prezzo del prodotto
- `category` (Category, nullable): Categoria associata

### Relazioni
- **ManyToOne** con Category: Un prodotto appartiene a una categoria
- Cascade persist: La categoria viene salvata automaticamente se nuova

### Metodi Principali
- `getId()`: Restituisce l'ID
- `getName()` / `setName(string)`: Gestione nome
- `getPrice()` / `setPrice(float)`: Gestione prezzo
- `getCategory()` / `setCategory(?Category)`: Gestione categoria

## Note sulle Relazioni
- Le relazioni sono bidirezionali
- Il cascade persist permette di creare categorie al volo quando si creano prodotti
- Le categorie possono esistere senza prodotti, ma i prodotti devono avere una categoria (opzionale nel codice)</content>
<parameter name="filePath">c:\Xiao\PCTO\progetto_fsl_qiu\docs\Entities.md