-- Quiz gaje 666 Database Setup
-- Run this script in your SQL tool (like phpMyAdmin) to create the necessary tables.

-- 1. Create the users table
-- This stores the registration data for your players.
CREATE TABLE IF NOT EXISTS users (
id INT(11) NOT NULL AUTO_INCREMENT,
username VARCHAR(50) NOT NULL,
password VARCHAR(255) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id),
UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create the leaderboard table
-- This stores the scores achieved by users in different categories.
CREATE TABLE IF NOT EXISTS leaderboard (
id INT(11) NOT NULL AUTO_INCREMENT,
username VARCHAR(50) NOT NULL,
score INT(11) NOT NULL,
category VARCHAR(50) NOT NULL,
played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;