# Task Manager AI

Task Manager AI is a modern, full-stack task management application built with Laravel 13, Vue 3, and Inertia.js. It features a sleek user interface with role-based access control and integrates with the Google Gemini API to automatically generate intelligent summaries and priority recommendations for your tasks!

---

## 🚀 Features

- **Vue 3 + Inertia.js Frontend:** A smooth, single-page application experience without writing custom APIs.
- **Tailwind CSS:** Modern, responsive design.
- **Google Gemini Integration:** AI-generated task summaries and automatic priority assignment based on task descriptions.
- **Repository Pattern Architecture:** Clean separation of concerns between business logic and database access.
- **Role-Based Access Control:** Separate interfaces and permissions for Admin and regular Users.
- **Queued Background Jobs:** AI summaries are generated in the background for optimal performance.

---

## 🛠️ Tech Stack

- **Backend:** Laravel 13 (PHP 8.2+)
- **Frontend:** Vue 3 (Composition API) + Inertia.js
- **Styling:** Tailwind CSS
- **Database:** MySQL
- **AI Integration:** Google Gemini API (`google-gemini-php/client`)

---

## ⚙️ Project Setup & Installation

Follow these steps to get the application running on your local machine:

### 1. Clone the repository
```bash
git clone https://github.com/C0deYell0w/task-manager-ai.git
cd task-manager-ai
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install NPM Dependencies
```bash
npm install
```

### 4. Configure Environment
Copy the example environment file:
```bash
cp .env.example .env
```
Generate the application key:
```bash
php artisan key:generate
```

### 5. Configure Database & AI
Open the `.env` file in your code editor and add your MySQL database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```
Also, ensure you add your Google Gemini API key at the bottom of the `.env` file:
```env
GEMINI_API_KEY=your_gemini_api_key_here
```

### 6. Run Migrations & Seeders
Create the database tables and populate them with default roles and users (Requires MySQL server to be running):
```bash
php artisan migrate --seed
```

### 7. Compile Frontend Assets
To compile the Vue assets for development:
```bash
npm run dev
```

### 8. Start the Development Server
In a new terminal window, start the Laravel server:
```bash
php artisan serve
```

You can now visit the app in your browser at `http://localhost:8000`.

---

## 📚 Application Architecture

- **`app/Repositories`**: Contains the `TaskRepositoryInterface` and Eloquent implementation to abstract database calls.
- **`app/Services`**: Contains `TaskService` for business logic and `AIService` for Gemini API interactions.
- **`app/Jobs`**: Contains `GenerateAISummaryJob` which processes AI requests asynchronously.
- **`resources/js/Pages`**: Contains the Vue components serving as Inertia pages.
