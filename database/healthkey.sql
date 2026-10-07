CREATE DATABASE IF NOT EXISTS healthkey CHARACTER SET utf8mb4;
USE healthkey;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(150) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,phone VARCHAR(30),role ENUM('user','admin') DEFAULT 'user',is_active TINYINT DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
CREATE TABLE patient_profiles(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL UNIQUE,date_of_birth DATE,blood_type VARCHAR(5),allergies TEXT,medical_conditions TEXT,medications TEXT,emergency_contact_name VARCHAR(100),emergency_contact_phone VARCHAR(30),emergency_contact_relationship VARCHAR(50),doctor_name VARCHAR(100),additional_notes TEXT,share_notes TINYINT DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
-- status: unclaimed (stock tag) -> claimed (owned, profile incomplete) -> active ; disabled by admin
CREATE TABLE qr_codes(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,unique_token CHAR(64) NOT NULL UNIQUE,status ENUM('unclaimed','claimed','active','disabled') DEFAULT 'unclaimed',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX(user_id),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL);
CREATE TABLE orders(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,order_number VARCHAR(30) NOT NULL UNIQUE,product_name VARCHAR(100),amount DECIMAL(8,2),payment_method VARCHAR(30),payment_status ENUM('pending','paid','cancelled') DEFAULT 'pending',qr_revealed TINYINT DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX(user_id),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE emergency_access_logs(id INT AUTO_INCREMENT PRIMARY KEY,qr_id INT NOT NULL,accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,ip_address VARCHAR(45),user_agent VARCHAR(255),FOREIGN KEY(qr_id) REFERENCES qr_codes(id) ON DELETE CASCADE);
CREATE TABLE chat_messages(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,sender ENUM('user','bot','admin') NOT NULL,message TEXT NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(user_id),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);

INSERT INTO users(id,name,email,password,phone,role) VALUES
(1,'HealthKey Admin','admin@healthkey.test','$2y$10$gvbrdFZEfOL52Qg6NNeqEeQiKBFRP6zysXPckQ5OaSxPS..h93URi','0123456789','admin'),
(2,'John Doe','user@healthkey.test','$2y$10$MWcyhFnqTwQlKMusYj4FCeilHOnQ8glf4AKE9tFdFGOky5QNzdS4G','0129876543','user');
INSERT INTO patient_profiles(user_id,date_of_birth,blood_type,allergies,medical_conditions,medications,emergency_contact_name,emergency_contact_phone,emergency_contact_relationship,doctor_name) VALUES
(2,'1998-05-14','O+','Penicillin','Asthma','Salbutamol inhaler','Jane Doe','012-3456789','Sister','Dr. Aminah');
INSERT INTO orders(user_id,order_number,product_name,amount,payment_method,payment_status,qr_revealed) VALUES(2,'HK-DEMO0001','HEALTHKEY Bracelet',29.90,'Demo Card','paid',1);
-- Demo active QR (John Doe): token = 0123456789abcdef x4
INSERT INTO qr_codes(user_id,unique_token,status) VALUES(2,REPEAT('0123456789abcdef',4),'active');
-- Demo UNCLAIMED stock tag for testing claim-qr.php: 64 x "b"
INSERT INTO qr_codes(user_id,unique_token,status) VALUES(NULL,REPEAT('b',64),'unclaimed');
