# Nasha Mukti Kendra - De-Addiction Management System

## Overview

Nasha Mukti Kendra is a comprehensive web-based management system designed to track and manage de-addiction centers and beneficiaries across India. The system provides a centralized platform for monitoring rehabilitation centers, managing beneficiary records, and generating statistical insights to support the Nasha Mukti initiative.

**Live Demo:** [https://nashamukti.ct.ws/](https://nashamukti.ct.ws/)

## Team

This project was developed by:
1. Aniket
2. Satyam Kumar
3. Shivam Rawat
4. Arnav Singh

## Features

- **Dashboard:** Real-time statistics and visualizations
  - Total centers count
  - Active beneficiaries
  - Success rate tracking
  - State-wise distribution
  - Addiction types distribution
  - Monthly admissions trends
  - Dark mode support

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
  - Advanced filtering by state, addiction type, and status

- **User Authentication:**
  - Role-based access control (Admin and Client roles)
  - Secure login and registration
  - Session management

## Project Structure

```
nashamukti/
├── assets/
│   └── images/
├── config/
│   ├── db.php
│   └── auth.php
├── includes/
│   └── header.php
├── index.php
├── about.php
├── records.php
├── statistics.php
├── add_center.php
├── add_beneficiary.php
├── login.php
├── register.php
├── logout.php
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
  - Font Awesome for icons
  - AOS (Animate On Scroll) for animations

- **Backend:**
  - PHP
  - MySQL/MariaDB
  - Session-based authentication

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

6. **users:**
   - id (Primary Key)
   - username
   - password
   - email
   - role (admin/client)
   - created_at

## Usage

1. **Initial Setup:**
   - Access `setup_db.php` to initialize the database with sample data
   - This will create necessary tables and insert initial data

2. **Authentication:**
   - Default admin credentials:
     - Username: admin
     - Password: admin123
   - Users can register for new accounts or log in with existing credentials

3. **Adding Centers:**
   - Navigate to the Add Center page (admin only)
   - Fill in center details
   - Submit the form to register a new center

4. **Managing Beneficiaries:**
   - Use the Add Beneficiary page to register new beneficiaries
   - Select the appropriate center
   - Enter beneficiary details and admission information

5. **Viewing Records:**
   - Access the Records page to view all beneficiary entries (admin only)
   - Use filters to search specific records
   - View detailed statistics on the Statistics page

6. **Theme Support:**
   - The system includes a dark mode toggle for comfortable viewing in different lighting conditions
   - Theme preference is saved in local storage

## Deployment

The website is currently hosted at [http://nashamukti.ct.ws/](http://nashamukti.ct.ws/).

To deploy on your own server:
1. Upload all files to your web server
2. Create a MySQL database and update the connection details in `config/db.php`
3. Run `setup_db.php` to initialize the database
4. Access the website through your domain

## Maintenance

- Regular database backups are recommended
- Monitor the `update_stats.php` script for statistical updates
- Keep the PHP and MySQL versions up to date

## Support

For technical support or queries, please contact the development team or raise an issue in the repository.

## License

This project is licensed under the MIT License. 
