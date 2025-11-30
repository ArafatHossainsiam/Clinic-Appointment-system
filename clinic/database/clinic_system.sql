
-- Create Database
CREATE DATABASE IF NOT EXISTS clinic_system;
USE clinic_system;

-- Users Table (for authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('assistant', 'doctor', 'patient') NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctors Table
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100) NOT NULL,
    qualifications TEXT,
    achievements TEXT,
    photo VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(20),
    experience_years INT,
    consultation_fee DECIMAL(10,2),
    slot_start_time TIME,
    slot_end_time TIME,
    max_slots INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Appointments Table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id VARCHAR(50) UNIQUE NOT NULL,
    patient_id VARCHAR(50) NOT NULL,
    doctor_id VARCHAR(50) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    problem_description TEXT,
    medical_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
);

-- Medical History Table
CREATE TABLE medical_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(50) NOT NULL,
    visit_date DATE NOT NULL,
    doctor_id VARCHAR(50) NOT NULL,
    problems TEXT,
    diagnosis TEXT,
    test_reports TEXT,
    visit_count INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
);

-- Prescriptions Table
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id VARCHAR(50) UNIQUE NOT NULL,
    appointment_id VARCHAR(50) NOT NULL,
    patient_id VARCHAR(50) NOT NULL,
    doctor_id VARCHAR(50) NOT NULL,
    medicines TEXT NOT NULL,
    recommended_tests TEXT,
    notes TEXT,
    next_visit_date DATE,
    next_visit_day VARCHAR(20),
    next_visit_time TIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
);

-- Documents Table (for storing medical documents)
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id VARCHAR(50) UNIQUE NOT NULL,
    patient_id VARCHAR(50) NOT NULL,
    document_type ENUM('prescription', 'test_report', 'medical_record', 'other') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_by VARCHAR(50) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert Sample Data

-- Sample Users (passwords are hashed - 'password123' for all)
INSERT INTO users (user_id, name, password, role, email, phone) VALUES
('AST001', 'Sarah Johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assistant', 'sarah.j@clinic.com', '123-456-7890'),
('DOC001', 'Dr. Michael Chen', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'dr.chen@clinic.com', '123-456-7891'),
('DOC002', 'Dr. Emily Rodriguez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'dr.rodriguez@clinic.com', '123-456-7892'),
('PAT001', 'John Smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'john.smith@email.com', '123-456-7893'),
('PAT002', 'Emma Williams', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'emma.w@email.com', '123-456-7894');

-- Sample Doctors
INSERT INTO doctors (doctor_id, name, specialty, qualifications, achievements, email, phone, experience_years, consultation_fee, slot_start_time, slot_end_time, max_slots) VALUES
('DOC001', 'Dr. Michael Chen', 'Cardiology', 'MBBS, MD (Cardiology), FACC', 'Published 50+ research papers, Award for Excellence in Cardiac Care 2023', 'dr.chen@clinic.com', '123-456-7891', 15, 150.00, '09:00:00', '17:00:00', 12),
('DOC002', 'Dr. Emily Rodriguez', 'Pediatrics', 'MBBS, MD (Pediatrics), FAAP', 'Best Pediatrician Award 2022, Specialist in Child Development', 'dr.rodriguez@clinic.com', '123-456-7892', 12, 120.00, '10:00:00', '16:00:00', 10);

-- Sample Appointments
INSERT INTO appointments (appointment_id, patient_id, doctor_id, appointment_date, appointment_time, status, problem_description) VALUES
('APT001', 'PAT001', 'DOC001', '2025-11-15', '10:00:00', 'pending', 'Chest pain and shortness of breath'),
('APT002', 'PAT002', 'DOC002', '2025-11-16', '14:00:00', 'confirmed', 'Child vaccination checkup');

-- Indexes for better performance
CREATE INDEX idx_appointments_patient ON appointments(patient_id);
CREATE INDEX idx_appointments_doctor ON appointments(doctor_id);
CREATE INDEX idx_appointments_date ON appointments(appointment_date);
CREATE INDEX idx_medical_history_patient ON medical_history(patient_id);
CREATE INDEX idx_prescriptions_patient ON prescriptions(patient_id);
