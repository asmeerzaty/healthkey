-- Run ONCE in phpMyAdmin (SQL tab, database: healthkey). Adds the Owner (superadmin) role, bracelet catalog and staff chat.
USE healthkey;
ALTER TABLE users MODIFY role ENUM('user','admin','superadmin') NOT NULL DEFAULT 'user';
CREATE TABLE IF NOT EXISTS bracelets(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,type VARCHAR(20) NOT NULL DEFAULT 'adult',material VARCHAR(40) NOT NULL DEFAULT 'Silicone',price DECIMAL(8,2) NOT NULL,stock INT NOT NULL DEFAULT 0,color1 CHAR(6) NOT NULL DEFAULT '14B8A6',color2 CHAR(6) NOT NULL DEFAULT '2563EB',description TEXT,is_active TINYINT NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX(is_active));
ALTER TABLE orders ADD COLUMN bracelet_id INT NULL AFTER user_id, ADD FOREIGN KEY (bracelet_id) REFERENCES bracelets(id) ON DELETE SET NULL;
CREATE TABLE IF NOT EXISTS staff_messages(id INT AUTO_INCREMENT PRIMARY KEY,admin_id INT NOT NULL,sender_role ENUM('super','admin') NOT NULL,message TEXT NOT NULL,is_read TINYINT NOT NULL DEFAULT 0,read_at DATETIME NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(admin_id,sender_role,is_read),FOREIGN KEY(admin_id) REFERENCES users(id) ON DELETE CASCADE);
INSERT INTO bracelets(id,name,type,material,price,stock,color1,color2,description) VALUES
(1,'Classic Silicone','adult','Silicone',29.90,100,'14B8A6','2563EB','Everyday, sport-ready'),
(2,'Steel Edge','adult','Stainless steel',59.90,40,'94A3B8','334155','Laser-engraved, refined'),
(3,'Kids Colour','kids','Silicone',29.90,60,'F59E0B','F0484B','Bright, tough, tear-resistant');
UPDATE orders SET bracelet_id=1 WHERE bracelet_id IS NULL;
INSERT INTO users(id,name,email,password,phone,role) VALUES(3,'HealthKey Owner','owner@healthkey.test','$2y$10$nl3dQxGlQveGJ6W.Q8YnSezXoZsHM3j9SCd33Yk87C8ToTev38mpe','0111111111','superadmin');
