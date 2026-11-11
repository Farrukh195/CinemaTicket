CREATE DATABASE IF NOT EXISTS cinema_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinema_db;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 phone VARCHAR(30) NULL,
 password VARCHAR(255) NOT NULL,
 role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE movies (
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(180) NOT NULL,
 genre VARCHAR(100) NOT NULL,
 duration INT NOT NULL,
 rating VARCHAR(10) NULL,
 poster VARCHAR(500) NULL,
 description TEXT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE halls (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(80) NOT NULL,
 capacity INT NOT NULL DEFAULT 40
) ENGINE=InnoDB;

CREATE TABLE seats (
 id INT AUTO_INCREMENT PRIMARY KEY,
 hall_id INT NOT NULL,
 seat_row CHAR(1) NOT NULL,
 seat_number INT NOT NULL,
 UNIQUE KEY unique_hall_seat(hall_id,seat_row,seat_number),
 CONSTRAINT fk_seats_hall FOREIGN KEY(hall_id) REFERENCES halls(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE showtimes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 movie_id INT NOT NULL,
 hall_id INT NOT NULL,
 starts_at DATETIME NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 CONSTRAINT fk_show_movie FOREIGN KEY(movie_id) REFERENCES movies(id),
 CONSTRAINT fk_show_hall FOREIGN KEY(hall_id) REFERENCES halls(id),
 INDEX idx_showtime_date(starts_at)
) ENGINE=InnoDB;

CREATE TABLE orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 total_amount DECIMAL(10,2) NOT NULL,
 status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_order_user FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE tickets (
 id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 showtime_id INT NOT NULL,
 seat_id INT NOT NULL,
 code VARCHAR(30) NOT NULL UNIQUE,
 price DECIMAL(10,2) NOT NULL,
 status ENUM('reserved','paid','cancelled') NOT NULL DEFAULT 'paid',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_ticket_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_ticket_show FOREIGN KEY(showtime_id) REFERENCES showtimes(id),
 CONSTRAINT fk_ticket_seat FOREIGN KEY(seat_id) REFERENCES seats(id),
 UNIQUE KEY unique_booked_seat(showtime_id,seat_id)
) ENGINE=InnoDB;

INSERT INTO halls(name,capacity) VALUES ('Толори 1',40),('Толори 2',30);

-- 40 ҷой барои толори 1 (A-D, 1-10)
INSERT INTO seats(hall_id,seat_row,seat_number)
SELECT 1, r.row_name, n.num
FROM (SELECT 'A' row_name UNION ALL SELECT 'B' UNION ALL SELECT 'C' UNION ALL SELECT 'D') r
CROSS JOIN (SELECT 1 num UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) n;

-- 30 ҷой барои толори 2 (A-C, 1-10)
INSERT INTO seats(hall_id,seat_row,seat_number)
SELECT 2, r.row_name, n.num
FROM (SELECT 'A' row_name UNION ALL SELECT 'B' UNION ALL SELECT 'C') r
CROSS JOIN (SELECT 1 num UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) n;

INSERT INTO movies(title,genre,duration,rating,description) VALUES
('Сафари кайҳонӣ','Фантастика',120,'8.2','Филми шавқовар дар бораи саёҳат ба кайҳон.'),
('Асрори шаҳр','Детектив',105,'7.8','Қиссаи ҷустуҷӯи сирри як шаҳри қадимӣ.'),
('Дӯстии ҳақиқӣ','Комедия',95,'8.0','Филми оилавӣ дар бораи дӯстӣ ва ҳамдигарфаҳмӣ.');

-- Сеансҳои намунавӣ барои санаҳои оянда; санаҳоро баъд аз ворид кардан дар панели админ ҳам илова кардан мумкин аст.
INSERT INTO showtimes(movie_id,hall_id,starts_at,price) VALUES
(1,1,DATE_ADD(NOW(),INTERVAL 1 DAY),25.00),
(2,2,DATE_ADD(NOW(),INTERVAL 1 DAY),20.00),
(3,1,DATE_ADD(NOW(),INTERVAL 2 DAY),18.00);

-- Администратори ибтидоӣ:
-- email: admin@cinema.local
-- password: Admin123!
-- Hash барои password_hash('Admin123!', PASSWORD_DEFAULT)
INSERT INTO users(full_name,email,phone,password,role) VALUES
('Администратор','admin@cinema.local','','$2y$12$M92TdW6kJF.sZQAKlwPwF.jl8wRtJptVS4N1B4qS5a7PIIHI8S/bW','admin');
