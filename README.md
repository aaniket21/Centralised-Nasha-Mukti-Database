# Nasha Mukti Kendra - De-Addiction Management System

## Overview

Nasha Mukti Kendra is a comprehensive web-based management system designed to track and manage de-addiction centers and beneficiaries across India. The system provides a centralized platform for monitoring rehabilitation centers, managing beneficiary records, and generating statistical insights to support the Nasha Mukti initiative.

## Features

- **Dashboard:** Real-time statistics and visualizations
  - Total centers count
  - Active beneficiaries
  - Success rate tracking
  - State-wise distribution
  - Addiction types distribution
  - Monthly admissions trends

- **Center Management:**
  - Add new rehabilitation centers
  - Track center capacity and occupancy
  - Manage center details (contact, location, etc.)

- **Beneficiary Management:**
  - Register new beneficiaries
  - Track admission dates
  - Monitor recovery status
  - Record interventions and outcomes

- **Records & Statistics:**
  - Comprehensive beneficiary records
  - Detailed statistical analysis
  - Interactive data visualizations
  - Filterable and searchable data

## Project Structure

```
nashamukti/
├── assets/
│   └── images/
├── config/
│   └── db.php
├── index.php
├── about.php
├── records.php
├── statistics.php
├── add_center.php
├── add_beneficiary.php
├── setup_db.php
├── update_stats.php
└── README.md
```

## Technology Stack

- **Frontend:**
  - HTML5
  - Tailwind CSS (via CDN)
  - JavaScript
  - Chart.js for data visualization

- **Backend:**
  - PHP
  - MySQL/MariaDB

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. **Set up your web server:**
   - Install XAMPP, WAMP, or MAMP
   - Place the project files in your web server's document root
   - For XAMPP: `C:\xampp\htdocs\nashamukti\`

2. **Database Setup:**
   - Create a new MySQL database named `nasha_mukti1_db`
   - The database tables will be automatically created when you first access the application
   - Run `setup_db.php` to initialize sample data

3. **Configuration:**
   - The database connection is configured in `config/db.php`
   - Default credentials:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: nasha_mukti1_db

## Database Schema

The system uses the following tables:

1. **centers:**
   - id (Primary Key)
   - name
   - address
   - state
   - city
   - contact_person
   - phone
   - email
   - capacity
   - created_at

2. **beneficiaries:**
   - id (Primary Key)
   - center_id (Foreign Key)
   - name
   - age
   - gender
   - address
   - phone
   - addiction_type
   - admission_date
   - status
   - created_at

3. **interventions:**
   - id (Primary Key)
   - beneficiary_id (Foreign Key)
   - intervention_type
   - description
   - date
   - outcome
   - created_at

4. **addiction_types:**
   - id (Primary Key)
   - name
   - count

5. **monthly_admissions:**
   - id (Primary Key)
   - month
   - year
   - count
   - created_at

## Usage

1. **Initial Setup:**
   - Access `setup_db.php` to initialize the database with sample data
   - This will create necessary tables and insert initial data

2. **Adding Centers:**
   - Navigate to the Add Center page
   - Fill in center details
   - Submit the form to register a new center

3. **Managing Beneficiaries:**
   - Use the Add Beneficiary page to register new beneficiaries
   - Select the appropriate center
   - Enter beneficiary details and admission information

4. **Viewing Records:**
   - Access the Records page to view all beneficiary entries
   - Use filters to search specific records
   - View detailed statistics on the Statistics page

## Maintenance

- Regular database backups are recommended
- Monitor the `update_stats.php` script for statistical updates
- Keep the PHP and MySQL versions up to date

## Support

For technical support or queries, please contact the development team or raise an issue in the repository.

## License

This project is licensed under the MIT License. 