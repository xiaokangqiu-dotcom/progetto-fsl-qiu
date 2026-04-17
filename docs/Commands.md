# Comandi Console

I comandi permettono di importare dati da file CSV nel database.

## ImportCategoriesCommand

**File**: `src/Command/ImportCategoriesCommand.php`
**Comando**: `php bin/console app:import-categories`

### Cosa fa
- Legge il file `data/categorie.csv`
- Crea nuove categorie nel database
- Salta la prima riga (intestazione)

### Formato CSV
```csv
name
Alimentari
Giocattoli
Sport
Arredamento
```

### Comportamento
- Se il file non esiste: errore e uscita
- Crea categorie con i nomi dal CSV
- Salva tutto in una volta con `flush()`
- Mostra messaggio di successo

## ImportProductsCommand

**File**: `src/Command/ImportProductsCommand.php`
**Comando**: `php bin/console app:import-products`

### Cosa fa
- Legge il file `data/prodotti.csv`
- Crea prodotti e categorie automaticamente
- Gestisce categorie esistenti o ne crea di nuove
- Usa cache in memoria per ottimizzare

### Formato CSV
```csv
name,price,category
Laptop Pro 16,1250.00,Informatica
Smartphone Galaxy,799.00,Telefonia
Mouse Gaming RGB,45.90,Periferiche PC
```

### Logica Intelligente delle Categorie
1. **Cache in memoria**: Tiene traccia delle categorie già elaborate nel comando
2. **Ricerca DB**: Se non in cache, cerca nel database
3. **Creazione automatica**: Se non esiste, crea nuova categoria
4. **Riutilizzo**: Usa categorie esistenti per prodotti successivi

### Comportamento
- Converte prezzo in float automaticamente
- Trim dei valori per rimuovere spazi
- Cascade persist: salva categorie nuove automaticamente
- Salva tutto in una volta alla fine
- Gestisce categorie duplicate nel CSV

## Come Usare

```bash
# Importa prima le categorie (opzionale, vengono create automaticamente con prodotti)
php bin/console app:import-categories

# Importa i prodotti (raccomandato)
php bin/console app:import-products
```

## File CSV
- `data/categorie.csv`: Solo colonna "name"
- `data/prodotti.csv`: Colonne "name,price,category"
- Separatore: virgola
- Prima riga: intestazione (saltata)</content>
<parameter name="filePath">c:\Xiao\PCTO\progetto_fsl_qiu\docs\Commands.md