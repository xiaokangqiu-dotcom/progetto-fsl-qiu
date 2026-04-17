# Progetto FSL Qiu - Documentazione

Questa documentazione ti aiuta a capire rapidamente il tuo progetto Symfony quando lo riapri tra due mesi.

## Panoramica del Progetto

Questo è un'applicazione Symfony che gestisce categorie e prodotti tramite API REST. Include funzionalità per creare, leggere, aggiornare e eliminare (CRUD) sia categorie che prodotti, oltre a comandi per importare dati da file CSV.

### Tecnologie Utilizzate
- **Symfony 7** (framework PHP)
- **Doctrine ORM** per la gestione del database
- **MySQL** come database (configurato in doctrine.yaml)
- **API REST** con risposte JSON
- **Comandi Console** per importare dati CSV

### Struttura del Database
- **Category**: id (int), name (varchar 255)
- **Product**: id (int), name (varchar 255), price (float), category_id (int, foreign key)

Relazione: Una categoria può avere molti prodotti (OneToMany).

## Come Avviare il Progetto

1. **Installa dipendenze**:
   ```bash
   composer install
   ```

2. **Configura il database**:
   - Modifica `config/packages/doctrine.yaml` per le tue credenziali DB
   - Crea il database se necessario

3. **Esegui le migrazioni**:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

4. **Avvia il server**:
   ```bash
   php bin/console cache:clear
   symfony server:start
   # o
   php -S localhost:8000 -t public
   ```

## Importazione Dati

Il progetto include due comandi per importare dati da CSV:

- **Importa categorie**:
  ```bash
  php bin/console app:import-categories
  ```

- **Importa prodotti** (include gestione automatica categorie):
  ```bash
  php bin/console app:import-products
  ```

I file CSV si trovano in `data/categorie.csv` e `data/prodotti.csv`.

## File Importanti

- `src/Entity/Category.php` e `src/Entity/Product.php`: Entità Doctrine
- `src/Controller/CategoryController.php` e `src/Controller/ProductController.php`: API endpoints
- `src/Command/ImportCategoriesCommand.php` e `src/Command/ImportProductsCommand.php`: Comandi console
- `migrations/`: Migrazioni database
- `config/`: Configurazioni Symfony

Consulta i file specifici in questa cartella docs per dettagli su ogni componente.</content>
<parameter name="filePath">c:\Xiao\PCTO\progetto_fsl_qiu\docs\README.md