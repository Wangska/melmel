# HikeBook Cebu - Hiking Booking Application

A complete PHP-based hiking booking system featuring 9 beautiful trails in Cebu, Philippines.

## Features

- **Browse Hikes**: View all available hiking trails with beautiful images
- **User Registration & Login**: Secure authentication system
- **Booking System**: Book hikes with date selection and guest count
- **User Profile**: View and manage your bookings
- **Admin Dashboard**: 
  - Manage all hikes (add, edit, delete)
  - Manage bookings (confirm, cancel)
  - View statistics and revenue
  - Monitor recent bookings

## Installation

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 7.4 or higher
- Modern web browser

### Setup Steps

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL

2. **Run the Installation Script**
   - Navigate to: `http://localhost/hikingphp/INSTALL.php`
   - This will:
     - Create the database
     - Create all tables
     - Insert sample hikes with images
     - Create admin account

3. **Access the Application**
   - Homepage: `http://localhost/hikingphp/`
   - Admin Login: `http://localhost/hikingphp/login.php`

**Note:** If images don't show, run `http://localhost/hikingphp/fix_images.php` once to update the database paths.

## Default Admin Credentials

```
Email: admin@hikebook.com
Password: admin123
```

**⚠️ Important**: Change the admin password after first login!

## Application Structure

```
hikingphp/
├── config.php              # Database configuration
├── index.php               # Homepage
├── hikes.php              # All hikes listing
├── hike_details.php       # Individual hike details
├── book.php               # Booking form
├── login.php              # User login
├── register.php           # User registration
├── profile.php            # User profile & bookings
├── logout.php             # Logout handler
├── INSTALL.php            # Installation script
├── admin/
│   ├── dashboard.php      # Admin dashboard
│   ├── manage_hikes.php   # Manage all hikes
│   ├── manage_bookings.php# Manage all bookings
│   ├── add_hike.php       # Add new hike
│   └── edit_hike.php      # Edit existing hike
├── css/
│   └── styles.css         # All styling
├── static/
│   └── images/            # Hike images
├── hiking_app.sql         # Database dump
└── README.md              # This file
```

## Available Hikes

1. **Osmeña Peak** - The highest point in Cebu
2. **Sirao Peak (Mt. Kan-Irag)** - Panoramic views of Metro Cebu
3. **Mt. Mago** - Gentle rolling hills with sea of clouds
4. **Casino Peak** - Chocolate Hills-like formations
5. **Mt. Naupa** - Perfect for easy day-hikes
6. **Mt. Manunggal** - Historically significant, challenging terrain
7. **Mt. Babag/RCPI Towers** - Classic Cebu City training climb
8. **Kandungaw Peak** - Epic ridge hike with thrilling viewpoints
9. **Mt. Lanaya** - Coastal peak with spectacular strait views

## User Features

### For Visitors
- Browse all available hikes
- Filter by difficulty level
- Search hikes by name or location
- View detailed hike information
- Register for an account

### For Registered Users
- All visitor features plus:
- Book hikes with preferred date
- Specify number of guests
- View booking history
- Track booking status

### For Administrators
- All user features plus:
- Add new hiking trails
- Edit existing hikes
- Delete hikes (if no bookings exist)
- View all bookings
- Update booking status (pending/confirmed/cancelled)
- View statistics:
  - Total hikes
  - Total bookings
  - Total users
  - Total revenue

## Database Schema

### Tables

1. **users** - User accounts (admin and regular users)
2. **hikes** - Hiking trail information
3. **bookings** - Hike reservations
4. **password_history** - Password tracking for security

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL (MariaDB)
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (via XAMPP)

## Features Highlights

### Modern UI/UX
- Responsive design for all devices
- Beautiful gradient backgrounds
- Smooth animations and transitions
- Card-based layout for hikes
- Color-coded difficulty badges

### Security
- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- Session-based authentication
- Password history tracking

### User Experience
- Intuitive navigation
- Real-time price calculation
- Search and filter functionality
- Status tracking for bookings
- Clear visual feedback

## Customization

### Adding More Hikes
1. Login as admin
2. Go to Admin Dashboard
3. Click "Manage Hikes"
4. Click "Add New Hike"
5. Fill in the details and submit

### Changing Colors
Edit `css/styles.css` and modify the CSS variables:
```css
:root {
    --primary: #2d5016;      /* Main green color */
    --accent: #ff6b35;       /* Orange accent */
    --text-dark: #2c3e50;    /* Text color */
    /* etc... */
}
```

### Database Configuration
Edit `config.php` to change database connection details:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hiking_app');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Troubleshooting

### Database Connection Error
- Make sure MySQL is running in XAMPP
- Check database credentials in `config.php`
- Ensure database exists (run INSTALL.php)

### Images Not Showing
- Verify images exist in `static/images/` folder
- Check file permissions
- Clear browser cache

### Cannot Login
- Ensure you've run INSTALL.php
- Try default admin credentials
- Check if session is working (session_start())

## Support

For issues or questions, please check:
1. XAMPP is running (Apache + MySQL)
2. Database is created (run INSTALL.php)
3. File permissions are correct
4. Browser console for JavaScript errors

## License

This is a sample project for educational purposes.

## Credits

Built with ❤️ for hiking enthusiasts in Cebu, Philippines.
