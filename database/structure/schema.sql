CREATE TABLE
    IF NOT EXISTS patient (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP
    );

CREATE TABLE
    IF NOT EXISTS doctor (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP
    );

CREATE TABLE
    IF NOT EXISTS medication (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        dose VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP
    );

CREATE TABLE
    IF NOT EXISTS patient_medication (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        doctor_id INT,
        medication_id INT,
        quantity INT,
        frequency VARCHAR(255),
        start_date DATE,
        end_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patient (id),
        FOREIGN KEY (doctor_id) REFERENCES doctor (id),
        FOREIGN KEY (medication_id) REFERENCES medication (id)
    );

CREATE INDEX idx_name ON patient (name);

CREATE INDEX idx_name ON doctor (name);

CREATE INDEX idx_name ON medication (name);

CREATE INDEX idx_patient_id ON patient_medication (patient_id);

CREATE INDEX idx_doctor_id ON patient_medication (doctor_id);

CREATE INDEX idx_medication_id ON patient_medication (medication_id);

CREATE INDEX idx_quantity ON patient_medication (quantity);

CREATE INDEX idx_start_date ON patient_medication (start_date);

CREATE INDEX idx_end_date ON patient_medication (end_date);