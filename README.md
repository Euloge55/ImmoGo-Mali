# ImmoGo Mali — Plateforme Immobilière

Plateforme de gestion immobilière pour le Mali.

---

## 🚀 Installation rapide (après `git clone`)

### 1. Installer les dépendances PHP
```bash
composer install
```

### 2. Copier le fichier d'environnement
```bash
cp .env.example .env
```
Puis éditer `.env` avec vos paramètres de base de données :
```
DB_DATABASE=immogo-mali
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Générer la clé de l'application
```bash
php artisan key:generate
```

### 4. Créer la base de données et lancer les migrations + seeders
```bash
php artisan migrate --seed
```
Cela crée automatiquement :
- Toutes les tables
- Les **11 régions du Mali** avec villes et quartiers
- Les **10 types de biens** (Appartement, Maison, Villa...)
- Les **5 modes de paiement** (CinetPay, Orange Money...)
- Le **compte Super Administrateur** (voir ci-dessous)

### 5. Créer le lien symbolique pour les photos
```bash
php artisan storage:link
```

### 6. Lancer le serveur
```bash
php artisan serve --port=8001
```

---

## 🔑 Identifiants Super Administrateur

| Champ | Valeur |
|---|---|
| **URL de connexion** | `http://127.0.0.1:8001/connexion` |
| **Email** | `zoumanadiabate48@gmail.com` |
| **Mot de passe** | `2004200z` |

---

## 👥 Rôles

| Rôle | Accès | Création |
|---|---|---|
| **Super Admin** | Tableau de bord global, agences, clients, contrats | Compte fixe (seeder) |
| **Admin Principal** | Biens, réservations, équipe, config paiement CinetPay | Créé par le Super Admin |
| **Admin Assistant** | Biens, réservations | Créé par l'Admin Principal |
| **Client** | Biens, réservations, favoris, profil | Inscription sur le site |

---

## 💳 Configuration CinetPay (par agence)

Chaque agence configure ses propres clés CinetPay depuis :
`/admin/paiement/config` (Admin Principal uniquement)

Obtenir les clés sur [cinetpay.com](https://www.cinetpay.com)

---

## 📁 Structure des dossiers importants

```
app/Http/Controllers/Web/   → Contrôleurs web
resources/views/            → Vues Blade
database/seeders/           → Données initiales
storage/app/public/biens/   → Photos des biens
storage/app/public/logos/   → Logos des agences
```

---

## ⚙️ Variables d'environnement importantes

```env
APP_NAME=ImmoGoMali
APP_URL=http://127.0.0.1:8001
DB_CONNECTION=mysql
DB_DATABASE=immogo-mali
SESSION_DRIVER=file
FILESYSTEM_DISK=public
```
