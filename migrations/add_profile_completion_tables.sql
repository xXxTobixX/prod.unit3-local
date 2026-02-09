-- Add profile_completed flag to users
ALTER TABLE `users` ADD COLUMN `profile_completed` BOOLEAN DEFAULT FALSE AFTER `status`;

-- Create Business Profiles Table
CREATE TABLE IF NOT EXISTS `business_profiles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `business_type` VARCHAR(100), -- sole prop / coop / association
    `sector` VARCHAR(100),
    `address` TEXT,
    `registration_number` VARCHAR(100), -- DTI / SEC / CDA
    `year_started` INT,
    `number_of_workers` INT,
    `compliance_type` VARCHAR(100), -- food, non-food, agri, etc.
    `data_consent` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create User Products Table (Initial Product)
CREATE TABLE IF NOT EXISTS `user_products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100),
    `description` TEXT,
    `production_capacity` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
