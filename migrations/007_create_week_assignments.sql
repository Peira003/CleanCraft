CREATE TABLE week_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    week_start DATE NOT NULL,
    user_id INT NOT NULL,
    UNIQUE KEY unique_week (plan_id, week_start),
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;