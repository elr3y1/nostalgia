CREATE TABLE vik_app_games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    fileName VARCHAR(255) NOT NULL,
    consola VARCHAR(100),
    cover VARCHAR(1024),
    disc VARCHAR(1024),
    manual VARCHAR(1024),
    logo VARCHAR(1024),
    gameplay VARCHAR(1024),
    soundtrack VARCHAR(1024)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_spanish_ci;
