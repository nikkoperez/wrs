<?php
header('Content-Type: application/json');
require_once 'src/Database/DBConnection.php';
require_once 'src/Class/PatientClass.php';

$database = new DBConnection();
$patientClass = new PatientClass($database);

echo " Get all patients that are taking one particular medication\n";
try {
    $medicationId = 1;
    $patientsByMedication = $patientClass->getPatientsByMedication($medicationId);
    if (!empty($patientsByMedication)) {
        echo " Patients taking medication with ID: $medicationId\n\n";
        foreach ($patientsByMedication as $patient) {
            echo " Patient Name: " . $patient['patient_name'] . "\n";
            echo " Medication Name: " . $patient['medication_name'] . "\n";
            echo " ------------------------------------------------------\n";
        }
        echo "\n\n";
    } else {
        echo " No patients found for medication with ID: $medicationId\n\n";
    }
} catch (TypeError $e) {
    echo " Error occurred: " . $e->getMessage() . " \n\n";
}

echo " Get all patients and prescriptions count for current year ordered by\n";
$patientMedicationCountCurYear = $patientClass->getPatientMedicationCountCurYear();
if (!empty($patientMedicationCountCurYear)) {
    echo " Patients and prescriptions count for current year\n\n";
    foreach ($patientMedicationCountCurYear as $count) {
        echo " Patient Name: " . $count['patient_name'] . "\n";
        echo " Prescription Count: " . $count['medication_count'] . "\n";
        echo " ------------------------------------------------------\n";
    }
    echo "\n\n";
} else {
    echo " No patients and prescriptions count for current year found\n";
    echo " ------------------------------------------------------\n";
}


echo " Get all medications for one particular patient. Returned data should include patient name, doctor name, medication and prescription information\n";
try {
    $patientId = 11;
    $patientMedications = $patientClass->getPatientMedications($patientId);
    if (!empty($patientMedications)) {
        echo " Patient medications for Patient ID: $patientId\n\n";
        foreach ($patientMedications as $patient) {
            echo " Patient Name: " . $patient['patient_name'] . "\n";
            echo " Doctor Name: " . $patient['doctor_name'] . "\n";
            echo " Medication Name: " . $patient['medication_name'] . "\n";
            echo " Quantity: " . $patient['quantity'] . "\n";
            echo " Frequency: " . $patient['frequency'] . "\n";
            echo " Start Date: " . $patient['start_date'] . "\n";
            echo " End Date: " . $patient['end_date'] . "\n";
            echo " ------------------------------------------------------\n";
        }
        echo "\n\n";
    } else {
        echo " Patient ID: $patientId does not exists\n";
        echo " ------------------------------------------------------\n";
    }
} catch (TypeError $e) {
    echo ' Error occurred: ' . $e->getMessage();
}

echo " Get all patients that prescribed more than one medication for the previous and current year\n";
$patientMedicationPrevCurYear = $patientClass->getPatientMedicationPrevCurYear();
if (!empty($patientMedicationPrevCurYear)) {
    echo " Patient medications for previous and current year\n\n";
    foreach ($patientMedicationPrevCurYear as $patient) {
        echo " Patient Name: " . $patient['patient_name'] . "\n";
        echo " Medication Count: " . $patient['medication_count'] . "\n";
        echo " Medication Names: " . $patient['medication_name'] . "\n";
        echo " Year: " . $patient['year'] . "\n";
        echo " ------------------------------------------------------\n";
    }
    echo "\n\n";
} else {
    echo " Patient medications for previous and current year not found\n";
    echo " ------------------------------------------------------\n";
}
