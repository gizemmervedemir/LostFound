# # Lost & Found Matching Platform

An online platform for matching lost and found items in environments like university campuses, public transportation systems, or shopping malls.

## Features

- User roles: Lost item reporter, Found item submitter, and Admin
- AJAX-based live search for matching items
- Image upload and processing capabilities
- Email notifications for matches and admin actions
- Admin panel for managing listings and user activity
- Session and cookie handling for user persistence
- Planned API endpoints for mobile app integration
- QR code generation for physical item labeling (future feature)

## Tech Stack

- Backend: PHP 8.1+
- Database: MySQL
- Frontend: HTML, CSS, JavaScript
- Email: PHPMailer
- Image Processing: Intervention Image
- Framework: Custom MVC

## Setup Instructions

1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Set up environment variables:
   ```bash
   cp .env.example .env
   # Edit .env with your configuration
   ```
4. Create database:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
5. Run the application:
   ```bash
   php -S localhost:8000
   ```

## Project Structure

```
lost-found-platform/
├── app/
│   ├── __init__.py
│   ├── models/
│   ├── routes/
│   ├── static/
│   └── templates/
├── config.py
├── requirements.txt
└── README.md
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request