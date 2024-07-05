-- Tạo user mới trong bảng users
INSERT INTO users (name, email, password, created_at, updated_at)
VALUES ('Admin', 'nimda@example.com', 'password', NOW(), NOW());

-- Lấy ID của user vừa tạo
SET @user_id = LAST_INSERT_ID();

-- Thêm bản ghi vào bảng admins
INSERT INTO admins (user_id, management_level, contact_phone)
VALUES (@user_id, 'Admin Level', 'Admin Contact Phone');