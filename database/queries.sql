
-- Lab 7: CinemaTicket
-- Database: cinema_db

USE cinema_db;
SET NAMES utf8mb4;

-- 1. Uchta jadvalni JOIN qilish
-- TF: Film namoyishlari va kinoteatr zallarini ko'rish
SELECT
    m.id,
    m.title,
    m.genre,
    h.name AS hall_name,
    s.starts_at
FROM showtimes s
JOIN movies m ON s.movie_id = m.id
JOIN halls h ON s.hall_id = h.id;

-- 2. LIKE orqali film qidirish
-- TF: Film nomi bo'yicha qidirish
SELECT id, title, genre
FROM movies
WHERE title LIKE '%Avatar%';

-- 3. GROUP BY + COUNT
-- TF: Har bir janr bo'yicha filmlar sonini hisoblash
SELECT
    genre,
    COUNT(*) AS films_count
FROM movies
GROUP BY genre;

-- 4. LEFT JOIN
-- TF: Barcha zallar va ulardagi joylar sonini ko'rish
SELECT
    h.id,
    h.name,
    h.capacity,
    COUNT(s.id) AS seats_count
FROM halls h
LEFT JOIN seats s ON h.id = s.hall_id
GROUP BY h.id, h.name, h.capacity;

-- 5. Holat bo'yicha filtrlash
-- TF: Kutilayotgan buyurtmalarni ko'rish
SELECT
    id,
    user_id,
    total_amount,
    status,
    created_at
FROM orders
WHERE status = 'pending';

-- 6. LIMIT / OFFSET
-- TF: Filmlarni sahifalarga ajratib ko'rish
SELECT id, title, genre, duration
FROM movies
ORDER BY id
LIMIT 5 OFFSET 0;
