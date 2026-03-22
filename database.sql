CREATE DATABASE IF NOT EXISTS cyclesense;

USE cyclesense;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    mode ENUM('regular', 'pregnancy', 'perimenopause') DEFAULT 'regular',
    privacy_mode BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS cycles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS predictions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    next_period_date DATE,
    ovulation_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS symptoms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE,
    mood VARCHAR(50),
    pain_level INT,
    flow VARCHAR(50),
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS health_tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    category VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    password VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin credentials (admin / admin123)
INSERT IGNORE INTO admins (username, password) VALUES ('admin', '$2y$10$wSVLNYeNNpyi4R5rv8ipX.1.xtdrntYYP6rGZp2OVMtHE2s.rVdDu');

-- New Feature Tables
CREATE TABLE IF NOT EXISTS pregnancies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    due_date DATE,
    current_week INT DEFAULT 1,
    baby_size_fruit VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS partner_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    primary_user_id INT,
    partner_user_id INT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (primary_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (partner_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category VARCHAR(100),
    title VARCHAR(255),
    content TEXT,
    is_anonymous BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS community_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    user_id INT,
    content TEXT,
    is_anonymous BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS medical_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    video_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS course_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    course_id INT,
    completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES medical_courses(id) ON DELETE CASCADE
);

-- Default Health Tips (seeded across all 4 cycle phases)
INSERT IGNORE INTO health_tips (id, title, content, category) VALUES
(1, 'Stay Hydrated During Menstruation', 'Drinking plenty of water helps reduce bloating and cramps during your period. Aim for at least 8 glasses a day.', 'menstrual'),
(2, 'Iron-Rich Foods', 'Replenish lost iron by eating leafy greens, beans, and red meat during your period to prevent fatigue.', 'menstrual'),
(3, 'Gentle Exercise Helps', 'Light stretching, yoga, and walks can ease menstrual cramps and improve your mood during your period.', 'menstrual'),
(4, 'Boost Energy in Follicular Phase', 'Your energy peaks after menstruation—great time to start new projects, exercise harder, and focus on goals.', 'follicular'),
(5, 'Eat for Estrogen Balance', 'Include flaxseeds, cruciferous vegetables, and whole grains to support healthy estrogen metabolism.', 'follicular'),
(6, 'Skin Care Routine', 'Estrogen rise during the follicular phase gives you glowing skin. Use lighter moisturizers and stay hydrated.', 'follicular'),
(7, 'Peak Fertility Awareness', 'Around ovulation you are most fertile. Track cervical mucus changes and basal body temperature for accuracy.', 'ovulation'),
(8, 'Social Energy Peaks at Ovulation', 'Hormones make you feel more confident and social around ovulation—schedule important meetings or dates now.', 'ovulation'),
(9, 'Light Ovulation Pain Is Normal', 'Mittelschmerz is mild one-sided pain during ovulation. A warm compress usually helps; see a doctor if severe.', 'ovulation'),
(10, 'Managing PMS Symptoms', 'During the luteal phase, reduce salt, caffeine, and sugar to minimize bloating, headaches, and mood swings.', 'luteal'),
(11, 'Prioritize Sleep', 'Progesterone rises in the luteal phase making you sleepier. Honor your body and aim for 8 hours of rest.', 'luteal'),
(12, 'Magnesium for Cramp Prevention', 'Taking magnesium supplements or eating bananas, dark chocolate, and nuts can reduce cramp severity before your period.', 'luteal'),
(13, 'Hydration in Pregnancy', 'Drinking plenty of water is crucial for amniotic fluid and increased blood volume during pregnancy.', 'pregnancy'),
(14, 'Folic Acid Support', 'Ensure you are getting enough folic acid through leafy greens and supplements to support your baby\'s neural tube development.', 'pregnancy'),
(15, 'Gentle Movement', 'Prenatal yoga or swimming are excellent ways to stay active and reduce back pain while pregnant.', 'pregnancy'),
(16, 'Cooling Strategies for Hot Flashes', 'If you experience hot flashes during perimenopause, try dressing in layers and keeping a fan nearby.', 'perimenopause'),
(17, 'Bone Health with Calcium', 'As estrogen levels shift in perimenopause, increase your calcium and Vitamin D intake to maintain bone density.', 'perimenopause'),
(18, 'Tracking Symptom Patterns', 'In perimenopause, periods can become irregular. Focus on tracking symptoms like mood shifts or sleep quality.', 'perimenopause'),
(19, 'General Pelvic Health', 'Performing regular Kegel exercises can improve pelvic floor strength and support overall reproductive health at any age.', 'general'),
(20, 'Screening and Checkups', 'Regular screenings and wellness checkups are essential for detecting any changes in your reproductive health early.', 'general');

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medical Courses Content
INSERT IGNORE INTO medical_courses (id, title, description, video_url) VALUES
(1, 'Understanding Your Cycle Phases', 'Learn about the follicular, ovulatory, luteal, and menstrual phases and how they affect your mood and energy.', 'https://www.youtube.com/embed/zcvo9VLVHWc'),
(2, 'Nutrition for Hormonal Balance', 'Discover which foods support healthy hormone production and which to avoid during your period to reduce cramps.', 'https://www.youtube.com/embed/J-y5Sq0YF0c'),
(3, 'Exercising with your Cycle', 'How to adapt your workout routine to match your body\'s natural rhythms for healthy living without burnout.', 'https://www.youtube.com/embed/JUnZQ38kdak');

-- Login Activity Tracking
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

