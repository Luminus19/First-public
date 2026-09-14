-- Omar's Hair Studio — bookings tablosu
-- PostgreSQL / Supabase için
CREATE TABLE bookings (
  id CHAR(36) PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  service VARCHAR(100) NOT NULL,
  preferred_date DATE NULL,
  preferred_time VARCHAR(10) NULL,
  notes TEXT NULL,
  status ENUM('new','confirmed','completed','cancelled') DEFAULT 'new',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX bookings_created_at_idx ON bookings (created_at);
CREATE INDEX bookings_status_idx ON bookings (status);
-- MySQL alternatifi:
-- CREATE TABLE bookings (
--   id CHAR(36) PRIMARY KEY,
--   full_name VARCHAR(100) NOT NULL,
--   phone VARCHAR(30) NOT NULL,
--   service VARCHAR(100) NOT NULL,
--   preferred_date DATE NULL,
--   preferred_time VARCHAR(10) NULL,
--   notes TEXT NULL,
--   status ENUM('new','confirmed','completed','cancelled') DEFAULT 'new',
--   created_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- );
