# Migrazioni Database

Le migrazioni Doctrine gestiscono l'evoluzione dello schema del database.

## Version20260410081021.php

**Data**: 10 aprile 2026
**Descrizione**: Creazione tabella prodotti iniziale

### up()
```sql
CREATE TABLE product (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
```

### down()
```sql
DROP TABLE product
```

**Cosa fa**: Crea la tabella `product` con id auto-incrementale, nome e prezzo.

## Version20260413130326.php

**Data**: 13 aprile 2026
**Descrizione**: Aggiunta categorie e relazione con prodotti

### up()
```sql
-- Crea tabella categorie
CREATE TABLE category (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`

-- Aggiunge colonna category_id alla tabella product
ALTER TABLE product ADD category_id INT DEFAULT NULL

-- Aggiunge foreign key constraint
ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2
    FOREIGN KEY (category_id) REFERENCES category (id)

-- Crea indice sulla foreign key
CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)
```

### down()
```sql
-- Rimuove tabella categorie
DROP TABLE category

-- Rimuove foreign key e indice
ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2
DROP INDEX IDX_D34A04AD12469DE2 ON product
ALTER TABLE product DROP category_id
```

**Cosa fa**: 
- Crea tabella `category`
- Aggiunge relazione ManyToOne da product a category
- Imposta category_id come nullable (prodotti possono esistere senza categoria)

## Come Eseguire le Migrazioni

```bash
# Vedi stato migrazioni
php bin/console doctrine:migrations:status

# Esegui tutte le migrazioni pendenti
php bin/console doctrine:migrations:migrate

# Torna indietro di una migrazione
php bin/console doctrine:migrations:migrate prev

# Crea nuova migrazione (dopo cambiamenti entità)
php bin/console doctrine:migrations:diff
```

## Schema Finale del Database

```
category
├── id (INT, PRIMARY KEY, AUTO_INCREMENT)
└── name (VARCHAR(255), NOT NULL)

product
├── id (INT, PRIMARY KEY, AUTO_INCREMENT)
├── name (VARCHAR(255), NOT NULL)
├── price (DOUBLE PRECISION, NOT NULL)
└── category_id (INT, FOREIGN KEY → category.id, NULLABLE)
```

**Relazione**: Un prodotto appartiene a una categoria (ManyToOne), una categoria può avere molti prodotti (OneToMany).</content>
<parameter name="filePath">c:\Xiao\PCTO\progetto_fsl_qiu\docs\Migrations.md