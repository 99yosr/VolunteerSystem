# 🤝 Unity Impact — Volunteer System

> Connecting associations and passionate volunteers to create lasting social change.

**Unity Impact** is a web platform built with **Symfony 6.4** that brings non-profit **associations** and **volunteers** together. Associations post their **events** and **trainings (formations)**. Volunteers browse them, sign up and take part. Associations then manage participants, view statistics and generate PDF flyers for their events.

---

## ✨ Features

### 👥 Two user roles
| Role | Description |
|------|-------------|
| `ROLE_ASSOCIATION` | An organization that creates and manages events and trainings |
| `ROLE_VOLONTAIRE` | A volunteer who explores opportunities and signs up |

After login, each user is sent to their own space based on their role.

### 🏢 For associations
- **Event management**: create, edit and delete events (name, start/end dates, location, description, image)
- **Training management**: create and manage trainings (title, dates, duration, certification)
- **Participant management**: see who joined each event or training and set their status (*actif / non actif*)
- **Statistics dashboard**: top events by participants and top trainings by enrollments
- **PDF flyer generation**: export a ready-to-print flyer for any event (Dompdf)
- **Association profile**: name, creation date, CEO, location, contact, description and logo

### 🙋 For volunteers
- Browse every event and training published by associations
- **Search** events and trainings by name
- **Join events** (`Participer`) and **enroll in trainings** (`Inscription`)
- See all past and current participations and enrollments on one page
- **Volunteer profile**: name, contact, skills, availability and photo

### 🔐 General
- Registration and login with Symfony Security (custom authenticator, hashed passwords)
- Role-based access control
- Image uploads through a dedicated `FileUploader` service
- "About" page presenting the platform's mission and vision

---

## 🛠️ Tech Stack

- **Backend:** PHP ≥ 8.1, Symfony 6.4
- **ORM / DB:** Doctrine ORM 3, Doctrine Migrations, MySQL
- **Templating:** Twig, Bootstrap
- **Frontend:** Symfony AssetMapper, Stimulus, Turbo (Symfony UX)
- **PDF:** Dompdf (`nucleos/dompdf-bundle`)
- **Other:** Symfony Forms, Validator, Mailer, Notifier, EasyAdmin

---

## 🗂️ Data Model

```
UserV (association or volunteer, distinguished by role)
 ├── 1..* Event        (created by an association)
 │        └── 1..* Participer   (volunteer ↔ event, with status)
 ├── 1..* Formation    (created by an association)
 │        └── 1..* Inscription  (volunteer ↔ training, with date & status)
 ├── 1..* Participer   (volunteer side)
 └── 1..* Inscription  (volunteer side)
```

| Entity | Purpose |
|--------|---------|
| `UserV` | Single user entity for both associations and volunteers |
| `Event` | An event organized by an association |
| `Formation` | A training session, with duration and certification |
| `Participer` | A volunteer's participation in an event |
| `Inscription` | A volunteer's enrollment in a training |

---

## 📁 Project Structure

```
VolunteerSystem/
├── assets/              # JS (Stimulus controllers) & CSS
├── config/              # Symfony configuration (security, doctrine, dompdf…)
├── migrations/          # Doctrine migrations
├── public/
│   ├── css/             # Page-specific styles
│   ├── img/             # Logos and static images
│   └── uploads/         # User-uploaded images
├── src/
│   ├── Controller/      # Event, Formation, Inscription, Participer, User, Security…
│   ├── Entity/          # Doctrine entities
│   ├── Form/            # Symfony form types
│   ├── Repository/      # Doctrine repositories
│   ├── Security/        # UserAuthenticator
│   └── Service/         # FileUploader, PdfGeneratorService
└── templates/           # Twig views (event, formation, user, security…)
```

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.1+
- Composer
- MySQL / MariaDB (e.g. through XAMPP or WAMP)
- [Symfony CLI](https://symfony.com/download) (recommended)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/<your-username>/VolunteerSystem.git
cd VolunteerSystem

# 2. Install PHP dependencies
composer install

# 3. Configure the database in .env.local
#    DATABASE_URL="mysql://root:@127.0.0.1:3306/volunteersystem"

# 4. Create the database and run migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Start the server
symfony server:start
# or: php -S localhost:8000 -t public
```

Then open **http://localhost:8000/login** in your browser.

---

## 🧭 Main Routes

| Route | Description |
|-------|-------------|
| `/register` | Create an account (association or volunteer) |
| `/login` / `/logout` | Authentication |
| `/accueil` | Volunteer home page |
| `/dashboard` | Association dashboard |
| `/dashboard/stat` | Association statistics |
| `/event` | Manage events (CRUD) |
| `/event/liste_events` | Browse events |
| `/event/{id}/flyer` | Download an event's PDF flyer |
| `/formation` | Manage trainings (CRUD) |
| `/formation/liste_formation` | Browse trainings |
| `/participer/par/{eventId}` | Join an event |
| `/inscription/insc/{formationId}` | Enroll in a training |
| `/profile` | User profile |
| `/apropos` | About page |

---

## 🔮 Possible Improvements
- Email notifications when someone signs up (Mailer and Notifier are already installed)
- Filter by date and location, plus pagination
- Full admin back-office with EasyAdmin
- Automated tests (PHPUnit)
- Multi-language support (FR / EN)

---

## 📄 License

This project was built for educational purposes.
