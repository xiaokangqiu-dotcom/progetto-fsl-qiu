# Dati e Importazione

## File CSV

I dati vengono importati da file CSV nella cartella `data/`.

### categorie.csv

**Percorso**: `data/categorie.csv`
**Usato da**: `ImportCategoriesCommand`

Contenuto esempio:
```csv
name
Alimentari
Giocattoli
Sport
Arredamento
```

**Struttura**:
- Prima riga: intestazione `name`
- Righe successive: nomi delle categorie
- Una categoria per riga

### prodotti.csv

**Percorso**: `data/prodotti.csv`
**Usato da**: `ImportProductsCommand`

Contenuto esempio:
```csv
name,price,category
Laptop Pro 16,1250.00,Informatica
Smartphone Galaxy,799.00,Telefonia
Monitor 4K 32 pollici,350.50,Informatica
Mouse Gaming RGB,45.90,Periferiche PC
Tastiera Meccanica Wireless,89.00,Periferiche PC
Cuffie Noise Cancelling,120.00,Audio
Speaker Bluetooth Portatile,55.00,Audio
SSD Esterno 1TB,110.00,Informatica
Sedia Ergonomica Ufficio,180.00,Arredamento
```

**Struttura**:
- Prima riga: intestazione `name,price,category`
- Colonna 1: nome prodotto (string)
- Colonna 2: prezzo (float con punto decimale)
- Colonna 3: nome categoria (string)

## Processo di Importazione

### 1. Importazione Categorie (Opzionale)
```bash
php bin/console app:import-categories
```
- Legge `categorie.csv`
- Crea categorie nel DB
- Utile se vuoi categorie senza prodotti

### 2. Importazione Prodotti (Raccomandato)
```bash
php bin/console app:import-products
```
- Legge `prodotti.csv`
- Per ogni riga:
  - Estrae nome, prezzo, categoria
  - Cerca categoria esistente nel DB
  - Se non esiste, la crea automaticamente
  - Crea il prodotto e lo associa alla categoria
- Gestisce cache per evitare duplicati

### Logica di Gestione Categorie
Il comando prodotti è "intelligente":

1. **Cache locale**: Tiene traccia categorie già elaborate
2. **Ricerca DB**: Se non in cache, cerca nel database
3. **Creazione**: Se non esiste, crea nuova categoria
4. **Associazione**: Collega prodotto alla categoria (vecchia o nuova)

### Esempio di Esecuzione
```
$ php bin/console app:import-products
Importazione completata! I prodotti (e le nuove categorie) sono nel database.
```

## Note Importanti
- I prezzi vengono convertiti automaticamente in float
- Gli spazi vengono rimossi con `trim()`
- Le categorie duplicate nel CSV vengono gestite (riutilizzate)
- Il cascade persist salva categorie nuove automaticamente
- Tutto viene salvato in una transazione alla fine (`flush()`)

## Verifica Importazione
Dopo l'importazione, puoi verificare con le API:

```bash
# Lista categorie
curl http://localhost:8000/api/categories

# Lista prodotti
curl http://localhost:8000/api/products
```</content>
<parameter name="filePath">c:\Xiao\PCTO\progetto_fsl_qiu\docs\Data.md