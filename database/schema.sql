-- =====================================================
-- CƠSỞ DỮ LIỆU QUẢN LÝ PHÒNG TRỌ (FINAL)
-- =====================================================

CREATE DATABASE IF NOT EXISTS quan_ly_phong_tro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quan_ly_phong_tro;

-- =====================================================
-- 1. USERS (Admin / Chủ trọ / Khách thuê)
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    cccd VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('USER','LANDLORD','ADMIN') NOT NULL,
    status ENUM('ACTIVE','BLOCKED') DEFAULT 'ACTIVE',
    must_change_password TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
);

-- =====================================================
-- 2. BUILDINGS (Tòa nhà)
-- =====================================================
CREATE TABLE buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(150) NOT NULL,
    address VARCHAR(255),
    floors INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (landlord_id, code),
    FOREIGN KEY (landlord_id) REFERENCES users(id),
    INDEX idx_landlord (landlord_id)
);

-- =====================================================
-- 3. ROOMS (Phòng trọ)
-- =====================================================
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    building_id INT NOT NULL,
    landlord_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    area FLOAT,
    description TEXT,
    status_room ENUM('EMPTY', 'RENTED') DEFAULT 'EMPTY',
    status ENUM('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES buildings(id),
    FOREIGN KEY (landlord_id) REFERENCES users(id),
    INDEX idx_building (building_id),
    INDEX idx_landlord (landlord_id),
    INDEX idx_status (status)
);

-- =====================================================
-- 4. ROOM_IMAGES (Ảnh phòng)
-- =====================================================
CREATE TABLE room_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_room (room_id)
);

-- =====================================================
-- 5. RENT_REQUESTS (Yêu cầu thuê)
-- =====================================================
CREATE TABLE rent_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    note VARCHAR(255),
    status ENUM('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_room (room_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);

-- =====================================================
-- 6. CONTRACTS (Hợp đồng)
-- =====================================================
CREATE TABLE contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('ACTIVE','ENDED') DEFAULT 'ACTIVE',
    deposit DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_room (room_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);

-- =====================================================
-- 7. SERVICES (Dịch vụ)
-- =====================================================
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(150) NOT NULL,
    type ENUM('FIXED','METERED') NOT NULL,
    unit VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (landlord_id, code),
    FOREIGN KEY (landlord_id) REFERENCES users(id),
    INDEX idx_landlord (landlord_id)
);

-- =====================================================
-- 8. ROOM_SERVICES (Phòng sử dụng dịch vụ)
-- =====================================================
CREATE TABLE room_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    service_id INT NOT NULL,
    calculation ENUM('PER_ROOM','PER_PERSON','PER_METER') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    UNIQUE (room_id, service_id),
    INDEX idx_room (room_id)
);

-- =====================================================
-- 9. INVOICES (Hóa đơn)
-- =====================================================
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    total DECIMAL(10,2),
    status ENUM('UNPAID','PAID') DEFAULT 'UNPAID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (contract_id, month, year),
    FOREIGN KEY (contract_id) REFERENCES contracts(id),
    INDEX idx_contract (contract_id),
    INDEX idx_status (status)
);

-- =====================================================
-- 10. INVOICE_ITEMS (Chi tiết hóa đơn)
-- =====================================================
CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    description VARCHAR(150),
    unit_price DECIMAL(10,2),
    quantity DECIMAL(10,2),
    amount DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    INDEX idx_invoice (invoice_id)
);

-- =====================================================
-- 11. MAINTENANCE (Bảo trì / Sự cố)
-- =====================================================
CREATE TABLE maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    reporter_name VARCHAR(100),
    content TEXT NOT NULL,
    level ENUM('LOW','MEDIUM','HIGH') DEFAULT 'MEDIUM',
    status ENUM('PENDING','PROCESSING','DONE') DEFAULT 'PENDING',
    cost DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    INDEX idx_room (room_id),
    INDEX idx_status (status)
);

-- =====================================================
-- 12. MAINTENANCE_IMAGES (Ảnh bảo trì)
-- =====================================================
CREATE TABLE maintenance_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maintenance_id INT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maintenance_id) REFERENCES maintenance(id) ON DELETE CASCADE,
    INDEX idx_maintenance (maintenance_id)
);

-- =====================================================
-- 13. FAVORITES (Phòng yêu thích)
-- =====================================================
CREATE TABLE favorites (
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, room_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    INDEX idx_user (user_id)
);

-- =====================================================
-- 14. EXTENSION_REQUESTS (Gia hạn hợp đồng)
-- =====================================================
CREATE TABLE extension_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    months INT NOT NULL COMMENT 'Số tháng muốn gia hạn',
    note VARCHAR(255) COMMENT 'Ghi chú của khách',
    status ENUM('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
    INDEX idx_contract (contract_id),
    INDEX idx_status (status)
);

-- =====================================================
-- 15. ACTIVITY_LOGS (Nhật ký hoạt động)
-- =====================================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    actor_id INT NOT NULL,
    actor_role VARCHAR(20) NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_id INT NULL,
    target_type VARCHAR(50) NULL,
    target_role VARCHAR(20) NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (actor_id) REFERENCES users(id),
    INDEX idx_actor (actor_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- 16. NOTIFICATIONS (Thông báo hệ thống)
-- =====================================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('REQUEST', 'INVOICE', 'MAINTENANCE', 'SYSTEM') DEFAULT 'SYSTEM',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_is_read (is_read)
);

-- =====================================================
-- 17. AREAS (Khu vực)
-- =====================================================
CREATE TABLE areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    status ENUM('ACTIVE', 'HIDDEN') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);

-- =====================================================
-- 18. PASSWORD_RESETS (Đặt lại mật khẩu)
-- =====================================================
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    temp_password VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
);

-- =====================================================
-- DỮ LIỆU MẪU
-- =====================================================

-- Admin user
INSERT INTO users (code, name, email, cccd, password, phone, role, status) VALUES
('ADMIN001', 'Quản Trị Viên', 'admin@localhost', '001001001', '$2y$10$Z.6xIdwpKNgFJdKLKwsqcuOMAJ8bOIHmYSTx.WvP7x9R.YpqM4Ehu', '0900000000', 'ADMIN', 'ACTIVE');

-- Landlord users
INSERT INTO users (code, name, email, cccd, password, phone, role, status) VALUES
('LANDLORD001', 'Chủ Trọ A', 'landlord@localhost', '001001002', '$2y$10$Z.6xIdwpKNgFJdKLKwsqcuOMAJ8bOIHmYSTx.WvP7x9R.YpqM4Ehu', '0901111111', 'LANDLORD', 'ACTIVE'),
('LANDLORD002', 'Chủ Trọ B', 'landlordB@localhost', '001001003', '$2y$10$Z.6xIdwpKNgFJdKLKwsqcuOMAJ8bOIHmYSTx.WvP7x9R.YpqM4Ehu', '0901111112', 'LANDLORD', 'ACTIVE');

-- Customer users
INSERT INTO users (code, name, email, cccd, password, phone, role, status) VALUES
('USER001', 'Khách Hàng 1', 'customer1@localhost', '001001004', '$2y$10$Z.6xIdwpKNgFJdKLKwsqcuOMAJ8bOIHmYSTx.WvP7x9R.YpqM4Ehu', '0902222222', 'USER', 'ACTIVE'),
('USER002', 'Khách Hàng 2', 'customer2@localhost', '001001005', '$2y$10$Z.6xIdwpKNgFJdKLKwsqcuOMAJ8bOIHmYSTx.WvP7x9R.YpqM4Ehu', '0903333333', 'USER', 'ACTIVE');

-- Buildings
INSERT INTO buildings (landlord_id, code, name, address, floors) VALUES
(2, 'BLD001', 'Tòa A - Trần Phú', '123 Trần Phú, Quận 1, TP.HCM', 5),
(2, 'BLD002', 'Tòa B - Nguyễn Huệ', '456 Nguyễn Huệ, Quận 3, TP.HCM', 3),
(3, 'BLD003', 'Tòa C - Lê Lợi', '789 Lê Lợi, Quận 1, TP.HCM', 4);

-- Rooms  
INSERT INTO rooms (building_id, landlord_id, title, price, area, description, status_room, status) VALUES
(1, 2, 'Phòng 101', 3000000, 25.5, 'Phòng thoáng mát, có ban công', 'EMPTY', 'APPROVED'),
(1, 2, 'Phòng 102', 2500000, 20.0, 'Phòng nhỏ, yên tĩnh', 'RENTED', 'APPROVED'),
(1, 2, 'Phòng 201', 3500000, 30.0, 'Phòng rộng, có cửa sổ lớn', 'EMPTY', 'APPROVED'),
(2, 2, 'Phòng 301', 4000000, 35.0, 'Phòng cao cấp, nội thất đầy đủ', 'EMPTY', 'PENDING'),
(2, 2, 'Phòng 302', 2800000, 22.0, 'Phòng tiết kiệm, mới sửa', 'EMPTY', 'APPROVED'),
(3, 3, 'Phòng 101', 2700000, 23.0, 'Phòng sang trọng', 'EMPTY', 'APPROVED');

-- Services
INSERT INTO services (landlord_id, code, name, type, unit, price, description) VALUES
(2, 'SVC001', 'Điện', 'METERED', 'kWh', 3500, 'Hóa đơn điện'),
(2, 'SVC002', 'Nước', 'METERED', 'm3', 8000, 'Hóa đơn nước'),
(2, 'SVC003', 'Internet', 'FIXED', 'tháng', 150000, 'Dịch vụ internet'),
(2, 'SVC004', 'Vệ sinh', 'FIXED', 'tháng', 100000, 'Dịch vụ vệ sinh chung'),
(3, 'SVC005', 'Điện', 'METERED', 'kWh', 3500, 'Hóa đơn điện');

-- Areas
INSERT INTO areas (name, status) VALUES
('Quận 1', 'ACTIVE'),
('Quận 3', 'ACTIVE'),
('Quận 7', 'ACTIVE'),
('Quận 5', 'ACTIVE');
