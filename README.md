# RentFlow

A modern Rental Property Management System built using CodeIgniter 4.

## Features

- User Authentication
- Role-Based Access (Admin, Owner, Tenant)
- Property Management
- Tenant Management
- Owner Management
- Lease Management
- Rent Collection
- Maintenance Requests
- Reports Dashboard
- Profile Management
- GSAP Animations
- Responsive UI

## Tech Stack

- PHP
- CodeIgniter 4
- MySQL
- Bootstrap 5
- JavaScript
- GSAP
- Chart.js

## Installation

1. Clone the repository

```bash
git clone https://github.com/surenther-10/RentFlow.git
```

2. Install dependencies

```bash
composer install
```

3. Copy the environment file

```bash
cp env .env
```

4. Configure your database in `.env`

5. Run migrations

```bash
php spark migrate
```

6. Seed the database

```bash
php spark db:seed RentalSeeder
```

7. Start the development server

```bash
php spark serve
```

Visit:

```
http://localhost:8080
```

## Project Structure

```
app/
public/
tests/
writable/
```

## Author

**Surenther**

Engineering Student | Full Stack Developer

GitHub:
https://github.com/surenther-10
