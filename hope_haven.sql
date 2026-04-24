-- ============================================================
-- Hope Haven – ENHANCED DATABASE v2.0
-- Real Orphanages: Nanded District, Maharashtra
-- ============================================================


DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS donation_requests;
DROP TABLE IF EXISTS orphanages;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor','ngo','admin') NOT NULL DEFAULT 'donor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orphanages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    taluka VARCHAR(100) NOT NULL DEFAULT '',
    location VARCHAR(255) NOT NULL,
    description TEXT,
    needs TEXT,
    contact VARCHAR(20),
    email VARCHAR(150),
    website VARCHAR(255) DEFAULT '',
    capacity INT DEFAULT 0,
    current_children INT DEFAULT 0,
    established_year INT DEFAULT 0,
    image_url VARCHAR(500) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE donation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    orphanage_id INT NOT NULL,
    type ENUM('food','clothes','money','medicines','books','other') NOT NULL,
    description TEXT,
    amount DECIMAL(10,2) DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    ngo_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (orphanage_id) REFERENCES orphanages(id) ON DELETE CASCADE
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    orphanage_id INT NOT NULL,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    purpose VARCHAR(255),
    visitors_count INT DEFAULT 1,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (orphanage_id) REFERENCES orphanages(id) ON DELETE CASCADE
);

-- Users (password = password123)
INSERT INTO users (name, email, password, role) VALUES
('Rahul Sharma',            'donor@hopehaven.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'donor'),
('Hope NGO Nanded',         'ngo@hopehaven.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ngo'),
('SSM Admin',               'admin1@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Balak Mandir Admin',      'admin2@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Sai Balgram Admin',       'admin3@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Mukhed Ashram Admin',     'admin4@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Mahur Ashram Admin',      'admin5@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Kinwat Bal Griha Admin',  'admin6@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Kandhar Kalyan Admin',    'admin7@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Hadgaon Sai Admin',       'admin8@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Degloor Bal Sadan Admin', 'admin9@hopehaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Bhokar Hope Admin',       'admin10@hopehaven.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Priya Mehta',             'priya@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'donor'),
('Arjun Kumar',             'arjun@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'donor');

-- Real Nanded District Orphanages
INSERT INTO orphanages (admin_id, name, taluka, location, description, needs, contact, email, website, capacity, current_children, established_year, image_url) VALUES

(3, 'Anand Balgram – Sanskriti Samvardhan Mandal', 'Biloli',
 'Sharadanagar, Sagroli Village, Taluka Biloli, Nanded – 431731',
 'Anand Balgram is the flagship orphanage wing of Sanskriti Samvardhan Mandal (SSM), a pioneering rural NGO founded in 1959 by Karmayogi Babasaheb K.N. Deshmukh. Spread across a 200-acre campus on the Balaghat Hills beside the Manjira River, this residential home provides orphaned and single-parented children with quality education, nutritious food, healthcare, sports training and vocational guidance. SSM''s Sagroli Sunrise Project has produced hundreds of national-level athletes who secured government employment.',
 'School uniforms, notebooks, sports shoes, milk powder, rice & dal, medicines, bedding sets, cricket equipment',
 '+91 2465 227848', 'info@ssmandal.net', 'https://ssmandal.net',
 120, 94, 1959,
 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80'),

(4, 'Balak Mandir – Sanskriti Samvardhan Mandal', 'Biloli',
 'Sharadanagar Campus, Sagroli, Taluka Biloli, Nanded – 431731',
 'Balak Mandir is SSM''s dedicated boys residential home on the vast Sharadanagar campus. Boys from destitute and single-parent families across Marathwada are sheltered here and receive holistic care — academic coaching, cultural activities, agriculture exposure and health check-ups. The serene campus setting beside the Manjira River provides an ideal environment for young minds to flourish.',
 'Winter jackets, gumboots, school bags, geometry boxes, cooking oil, lentils, first-aid supplies, sports jerseys',
 '+91 2465 227848', 'balakmandir@ssmandal.net', 'https://ssmandal.net',
 80, 61, 1965,
 'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?w=800&q=80'),

(5, 'Shri Sai Balgram Anath Ashram', 'Nanded',
 'Dnyaneshwar Nagar, HUDCO Colony, Nanded City – 431603',
 'Shri Sai Balgram Anath Ashram has been serving abandoned, orphaned and destitute children in Nanded city since 1998. Located near HUDCO Colony, the ashram admits children referred by Nanded District Court, police and social workers. Children receive free education, regular medical check-ups at Government Medical College Nanded, and weekend skill workshops. The ashram follows a family-style house-parent model so every child feels truly at home.',
 'Tiffin boxes, school shoes, blankets, sanitary supplies, vitamin supplements, Marathi & English books',
 '+91 2462 225311', 'saisaigram.nanded@gmail.com', '',
 70, 52, 1998,
 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=800&q=80'),

(6, 'Savitribai Phule Bal Kalyan Ashram', 'Mukhed',
 'Near Pandharinath Railway Station, Taluka Mukhed, Nanded – 431715',
 'Named in honour of India''s pioneering woman educator, this ashram in Mukhed Taluka provides free residential care to orphaned girls and boys from backward communities. Established under Pandharinath Sevabhavi Sanstha, the ashram gives special focus to girl-child education in a region where school dropout rates are high. Children participate in cultural programmes, yoga and community health campaigns.',
 'Girls'' uniforms, sanitary napkins, cooking gas, educational toys, reference books, medicines for common ailments',
 '+91 98222 62100', 'savitribaiphule.mukhed@gmail.com', '',
 60, 44, 2004,
 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?w=800&q=80'),

(7, 'Mata Renuka Bal Ashram', 'Mahur',
 'Near Renuka Devi Temple Road, Mahur, Taluka Mahur, Nanded – 431745',
 'Mahur is one of the 51 Shakti Peethas and a major pilgrimage town in Marathwada. This ashram, run by a temple trust, shelters tribal and nomadic children from the forested Kinwat–Mahur belt. Many children here belong to the Kolam and Gond tribal communities. The ashram connects education with cultural heritage through tribal art, folk music and nature-based learning alongside the state curriculum.',
 'Tribal art supplies, warm clothing, canvas shoes, nutritional supplements, notebooks, kerosene lanterns for study',
 '+91 77450 63300', 'matarenuka.ashram@gmail.com', '',
 55, 38, 2001,
 'https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=800&q=80'),

(8, 'Shivray Bal Griha', 'Kinwat',
 'Markenday Chowk, Bodhadi BK, Taluka Kinwat, Nanded – 431804',
 'Shivray Bal Griha is a small but dedicated children''s home in Kinwat Taluka — one of the remotest and most forested areas of Nanded district, bordering Telangana. Run by Shivray Bahuuddeshiya Sevabhavi Sanstha, this home primarily shelters tribal children who have lost parents to farming distress or illness. The ashram runs an attached primary school so children do not need to travel through the dense forest.',
 'Rice, cooking oil, jaggery, sorghum, warm blankets, pencil sets, medicines for malaria & fever, mosquito nets',
 '+91 77450 63358', 'shivraybalgriha@gmail.com', '',
 45, 31, 2008,
 'https://images.unsplash.com/photo-1526958977630-3b7fa53bf5d0?w=800&q=80'),

(9, 'Kandhar Bal Kalyan Grih', 'Kandhar',
 'Near Kandhar Fort Road, Taluka Kandhar, Nanded – 431714',
 'Kandhar is historically significant for its ancient Yadava-era fort and serves as a key taluka of southern Nanded. The Kandhar Bal Kalyan Grih serves orphaned and semi-orphaned children from Kandhar and Loha Taluka. Children are inspired by the region''s Maratha heritage. The ashram has an excellent academic track record — several alumni have secured government jobs and engineering admissions on merit.',
 'Bedsheets, pillow covers, geometry sets, Marathi medium textbooks, lentils, soybean oil, hygiene supplies, study lamps',
 '+91 94224 71700', 'kandharbalkalyan@gmail.com', '',
 65, 48, 1995,
 'https://images.unsplash.com/photo-1491156855053-9cdff72c7f85?w=800&q=80'),

(10, 'Saipratishthan Bal Ashram', 'Hadgaon',
 'Unchegaon BK, Taluka Hadgaon, Nanded – 431712',
 'Established by Saipratishthan Sevabhavi Sanstha, this ashram in Hadgaon Taluka follows the Sai philosophy of treating every child as a manifestation of God. All children regardless of religion are admitted and served with equal care. Vocational training in tailoring, carpentry and basic computer operation is offered to teenage residents, ensuring livelihood readiness after completing their stay.',
 'Sewing machines, carpentry tools, computers & UPS, vegetables, pulses, medicines, soap & toothpaste kits, fans',
 '+91 82754 53800', 'saipratishthan.hadgaon@gmail.com', '',
 50, 36, 2003,
 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=800&q=80'),

(11, 'Azrat Rafai Bal Sadan', 'Degloor',
 'Khaja Baba Nagar, Degloor Town, Taluka Degloor, Nanded – 431717',
 'Azrat Rafai Bal Sadan is a minority-community children''s home in Degloor Taluka serving Muslim orphans and destitute children across Marathwada. Run by Azrat Rafai Alpsankhyank Bahu Uddeshiya Sevabhavi Sanstha, the home maintains secular values while preserving the cultural identity of its children. A madrasa-cum-school setup ensures children receive both religious and modern mainstream education.',
 'Abayas & uniforms, Urdu-medium textbooks, prayer mats, dates & dry fruits, medicines, bicycles for older students',
 '+91 99604 29700', 'azratrafi.degloor@gmail.com', '',
 55, 40, 2000,
 'https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?w=800&q=80'),

(12, 'Hope Foundation Bal Griha', 'Bhokar',
 'Bhimrao Marg, Govindrao Chowk, Bhokar Town, Taluka Bhokar, Nanded – 431801',
 'Hope Foundation Bal Griha was established in 2010 by local social workers in response to Marathwada''s severe farmer-distress crisis, which left hundreds of children fatherless. The home specifically prioritises children of farmer-suicide victims, providing them a stable environment to heal, study and hope again. Counselling sessions, art therapy and peer-support groups are integral parts of the programme.',
 'Art therapy materials, counselling books, sports equipment (cricket & kabaddi), vegetables, eggs, milk, blankets, school shoes',
 '+91 84212 10100', 'hopefoundation.bhokar@gmail.com', '',
 60, 43, 2010,
 'https://images.unsplash.com/photo-1578357078586-491adf1aa5ba?w=800&q=80');

-- Sample donations
INSERT INTO donation_requests (donor_id, orphanage_id, type, description, amount, status, ngo_note) VALUES
(1,  1, 'food',    '25 kg rice, 10 kg dal, cooking oil for one month',        NULL,    'approved', 'Approved. Please coordinate with SSM office for handover.'),
(1,  3, 'clothes', 'Winter jackets for 20 children aged 6-14',                NULL,    'pending',  NULL),
(13, 2, 'money',   'Monthly contribution towards education expenses',          5000.00, 'approved', 'Thank you! Funds used for school fees.'),
(14, 4, 'medicines','First aid kit, paracetamol, ORS sachets, antiseptic',    NULL,    'approved', 'Medicines received. Thank you.'),
(1,  5, 'books',   'Marathi medium textbooks Std 1-7, 30 sets',               NULL,    'pending',  NULL),
(13, 6, 'food',    'Rice 50 kg, sorghum 20 kg, jaggery 5 kg',                 NULL,    'rejected', 'Unable to store bulk grains currently. Try next month.'),
(14, 7, 'money',   'One-time donation for vocational training equipment',      10000.00,'pending',  NULL),
(1,  8, 'clothes', 'School uniforms (boys & girls) for 25 children',          NULL,    'approved', 'Approved. Coordinate with ashram manager.'),
(13, 9, 'other',   'Prayer mats (50 nos) and Urdu textbooks (30 sets)',       NULL,    'pending',  NULL),
(14, 10,'food',    'Vegetables, eggs and milk for one week for 43 children',  NULL,    'approved', 'Fresh produce donation approved. Bring Monday morning.');

-- Sample appointments
INSERT INTO appointments (donor_id, orphanage_id, visit_date, visit_time, purpose, visitors_count, status, admin_note) VALUES
(1,  1, DATE_ADD(CURDATE(), INTERVAL ((8 - DAYOFWEEK(CURDATE())) % 7) DAY), '10:00:00', 'Sunday Caring Connections – story-telling & games', 4, 'approved', 'Welcome! Please arrive at SSM main gate.'),
(13, 3, DATE_ADD(CURDATE(), INTERVAL ((8 - DAYOFWEEK(CURDATE())) % 7 + 7) DAY), '11:00:00', 'Weekly volunteer visit and donation handover', 2, 'pending',  NULL),
(14, 5, DATE_ADD(CURDATE(), INTERVAL ((8 - DAYOFWEEK(CURDATE())) % 7 + 7) DAY), '09:30:00', 'Cultural programme participation', 5, 'approved', 'Bring ID proof at the gate.'),
(1,  7, DATE_ADD(CURDATE(), INTERVAL ((8 - DAYOFWEEK(CURDATE())) % 7 + 14) DAY), '10:30:00', 'Sunday Caring Connections – art & craft session', 3, 'pending', NULL),
(13, 10, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', 'Weekday visit to hand over vegetables and eggs', 2, 'approved', 'Approved. Contact manager before arrival.');
