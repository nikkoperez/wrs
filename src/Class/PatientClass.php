<?php
require_once 'src/Database/DBConnection.php';

class PatientClass
{
    private $dbConnection;

    public function __construct(DBConnection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Get all patients that are taking one particular medication
     *
     * @param int $medicationId
     * @return array
     */
    public function getPatientsByMedication(int $medicationId): array
    {
        $result = $this->dbConnection->select(
            'patient_medication',
            ['patient.name AS patient_name', 'medication.name AS medication_name'],
            [
                'patient ON patient.id = patient_medication.patient_id',
                'medication ON medication.id = patient_medication.medication_id'
            ],
            '',
            'medication_id = ?',
            'patient.id',
            '',
            '',
            [$medicationId]
        );

        return $result;
    }

    /**
     * Get all patients and prescriptions count for current year ordered by
     *
     * @return array
     */
    public function getPatientMedicationCountCurYear(): array
    {
        $result = $this->dbConnection->select(
            'patient',
            ['patient.name AS patient_name', 'COUNT(patient_medication.medication_id) AS medication_count'],
            [
                'patient_medication ON patient.id = patient_medication.patient_id',
            ],
            '',
            "patient_medication.start_date >= DATE_FORMAT(CURRENT_DATE, '%Y-01-01') AND patient_medication.end_date <= DATE_FORMAT(CURRENT_DATE, '%Y-12-31')",
            'patient.id',
            '',
            'medication_count DESC, patient.id'
        );

        return $result;
    }

    /**
     * Get all medications for one particular  patient. Returned data should include patient name, doctor name, medication and prescription information
     *
     * @param int $patientId
     * @return array
     */
    public function getPatientMedications(int $patientId): array
    {
        $result = $this->dbConnection->select(
            'patient',
            [
                'patient.name AS patient_name',
                'doctor.name AS doctor_name',
                'medication.name AS medication_name',
                'patient_medication.quantity',
                'patient_medication.frequency',
                'patient_medication.start_date',
                'patient_medication.end_date'
            ],
            [
                'patient_medication ON patient.id = patient_medication.patient_id',
                'doctor ON doctor.id = patient_medication.doctor_id',
                'medication ON medication.id = patient_medication.medication_id'
            ],
            'LEFT',
            'patient.id = ?',
            '',
            '',
            '',
            [$patientId]
        );

        return $result;
    }

    /**
     * Get all patients that prescribed more than one medication for the previous and current year
     *
     * @return array
     */
    public function getPatientMedicationPrevCurYear(): array
    {
        $result = $this->dbConnection->select(
            'patient_medication',
            [
                'patient.name AS patient_name',
                'COUNT(patient_medication.medication_id) AS medication_count',
                'GROUP_CONCAT(medication.name SEPARATOR ", ") AS medication_name',
                'YEAR(patient_medication.start_date) AS year'
            ],
            [
                'patient ON patient.id = patient_medication.patient_id',
                'medication ON medication.id = patient_medication.medication_id'
            ],
            '',
            '',
            'patient_medication.patient_id, year',
            'COUNT(patient_medication.medication_id) > 1'
        );

        return $result;
    }

    /**
     * Insert data into the specified table.
     *
     * @param string $table The name of the table to insert data into
     * @param array $dataSets The data to be inserted into the table
     * @return int The number of rows affected by the insert operation
     */
    public function insertData(string $table, array $dataSets): int
    {
        return $this->dbConnection->insert($table, $dataSets);
    }

    /**
     * Update data in the specified table.
     *
     * @param string $table The name of the table to update.
     * @param array $data The sets of data to update.
     * @param array $condition The condition for the update.
     * @param array $params Parameters for the condition.
     * @return int
     */
    public function updateData(string $table, array $data, array $condition, array $params = []): int
    {
        return $this->dbConnection->update($table, $data, $condition, $params);
    }

    /**
     * Deletes data from a specific table based on the given condition.
     *
     * @param string $table The name of the table from which data will be deleted
     * @param array $condition An array specifying the condition for deleting data
     * @param array $params Additional parameters (optional) to be used in the deletion process
     * @return int The number of rows affected by the deletion operation
     */
    public function deleteData(string $table, array $condition, array $params = []): int
    {
        return $this->dbConnection->delete($table, $condition, $params);
    }
}
