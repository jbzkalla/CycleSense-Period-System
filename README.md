# CycleSense - Intelligent Period & Fertility Tracker

Designed by **Kato Joseph Bwanika. 0708419371**.

CycleSense is a comprehensive reproductive health tracking platform built for the student community of Nkozi. It allows users to track their cycles, log symptoms across various modes (Regular, Pregnancy, and Perimenopause), and engage anonymously in secure communities.

## Major Features

1. **Cycle Tracking & Prediction**
   - Track period start/end dates.
   - Intelligent predictions for future ovulation windows and period starts.

2. **Tailored Tracking Modes**
   - **Regular Mode**: Standard cycle and fertility window tracking.
   - **Pregnancy Mode**: Week-by-week baby size tracker and due date countdown.
   - **Perimenopause Mode**: Focused on symptom logging for transitional health periods.

3. **Partner Sharing**
   - Securely invite a partner via email to share cycle phases and mood data to improve mutual understanding and communication.

4. **Secret Chats & Community**
   - Engage with the CycleSense community strictly anonymously using aliases, or use your public username if you prefer. Ideal for discussing sensitive reproductive health topics openly.

5. **Advanced Health Reports**
   - Produce a doctor-exportable printable HTML format showcasing patient history, the last 12 recorded cycles, and the most recent 50 symptom logs.

6. **Medical Educational Courses**
   - A video-embedded repository categorized into women's health courses. Users can learn from curated videos and mark them as complete.

## Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL Server
- **Frontend**: HTML5, Vanilla CSS, Bootstrap 5, FontAwesome, Chart.js

## Installation Guide for XAMPP

1. **Clone the Repository**
   Move the `CycleSense` directory into your `htdocs` folder:
   `C:\xampp\htdocs\BRONIA\CycleSense\`

2. **Configure Database**
   - Open XAMPP Control Panel.
   - Start **Apache** and **MySQL**.
   - Navigate to `http://localhost/phpmyadmin/`.
   - Create a database called `cyclesense`.
   - Import the completely structured `database.sql` file located in the root of the project to spawn the tables.

3. **Access the System**
   - Navigate to `http://localhost/BRONIA/CycleSense/` in any modern web browser.
   - See the **Login Credentials** section below to sign in.

## Login Credentials

### Database Connection

| Parameter | Value       |
|-----------|-------------|
| Host      | `localhost` |
| Username  | `root`      |
| Password  | *(empty)*   |
| Database  | `cyclesense`|

### Admin Panel

| Field    | Value      |
|----------|------------|
| URL      | `http://localhost/BRONIA/CycleSense/admin/login.php` |
| Username | `admin`    |
| Password | `admin123` |

> The default admin account is seeded automatically when you import `database.sql`.

### User Account

| Field    | Value |
|----------|-------|
| URL      | `http://localhost/BRONIA/CycleSense/auth/login.php` |
| Email    | Register a new account via the **Sign Up** page |
| Password | Minimum 6 characters |

> No demo user is pre-seeded. Navigate to `http://localhost/BRONIA/CycleSense/auth/register.php` to create one.

## Credits & Attribution

- Built as an independent project by **Kato Joseph Bwanika. 0708419371**. 
- Designed with students in Nkozi in mind.

---
&copy; CycleSense Nkozi. All Rights Reserved.
