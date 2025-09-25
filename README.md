# VetCare Pro - Veterinary Practice Management System

A comprehensive web-based system for managing veterinary practices, built with PHP, MySQL, HTML, CSS, and Bootstrap.

## Features

### Reception Module
- Patient registration with owner details
- Appointment booking and scheduling
- Queue management system
- Patient search and management
- View and update appointment status
- Clean, modern dashboard UI

### Doctor Module
- View daily appointments
- Record medical diagnoses, treatments, and prescriptions
- Clinical examination entry (TPR, physical exam, notes)
- Lab work and diagnostic imaging fields
- Upload and attach medical files/images
- Mark appointments as complete
- View patient medical history
- Modern, professional dashboard UI

### Billing & Records Module
- Track consultation and treatment costs
- Generate and print invoices
- Search medical records by patient or date
- Payment status tracking
- Export billing and record data

### Admin Module
- Staff management (add/edit doctors, receptionists, admins)
- System reports and analytics (Chart.js)
- Revenue tracking
- Appointment statistics
- Role-based access control
- User account management

### General Features
- Responsive, accessible design (Bootstrap 5)
- Unified, professional color scheme (Infinity Lines of Code)
- Secure login and session management
- Password hashing and input validation
- File upload and attachment management
- Broken link cleanup and navigation improvements
- Attribution to Infinity Lines of Code for design and UI

## Installation On Local Machine for Development

### Prerequisites
- XAMPP (or similar Apache/MySQL/PHP stack)
- Web browser

### Setup Steps

1. **Clone or Download** the project to your XAMPP htdocs directory:
   ```
   htdocs/VetSystem/Animedi/
   ```

2. **Create Database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `vetcare_pro`
   - Import the `schema.sql` file

3. **Configure Database Connection**:
   - Edit `includes/dbconn.php` if needed (default settings should work with XAMPP)

4. **Access the System**:
   - Open http://localhost/VetSystem/Animedi/ in your browser
   - This will redirect to the login page

### Default Login Credentials
- **Admin**: admin@vetcare.com / password (change after first login)
- **Doctor**: doctor@vetcare.com / password
- **Reception**: reception@vetcare.com / password

## Database Schema

The system uses MySQL with the following tables:
- `users` - Staff accounts
- `owners` - Pet owners
- `patients` - Animal patients
- `appointments` - Scheduled appointments
- `records` - Medical records
- `billing` - Payment records

## Technologies Used

- **Frontend**: HTML5, CSS3, Bootstrap 5, FontAwesome icons
- **Backend**: PHP 8+
- **Database**: MySQL
- **Charts**: Chart.js (for admin reports)

## Security Features

- Role-based access control
- Password hashing
- Session management
- Input validation

## Browser Compatibility

- Chrome/Chromium
- Firefox
- Safari
- Edge

## Support

For issues or questions, please check the code comments or create an issue in the repository.

## License

This project is open-source and available under the MIT License.