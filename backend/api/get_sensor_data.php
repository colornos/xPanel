<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: https://www.colornos.com");  // Allow only your frontend domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");       // Allow specific methods
header("Access-Control-Allow-Headers: Content-Type, Authorization");  // Allow necessary headers
header("Access-Control-Allow-Credentials: true");  // If credentials like cookies are needed

// Set the user ID variable
$id = 3;

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";

// Create connections for each sensor database
$connWeight = new mysqli($servername, $username, $password, "");
$connTemperature = new mysqli($servername, $username, $password, "");
$connGlucose = new mysqli($servername, $username, $password, ""); // Corrected here
$connBloodOxygen = new mysqli($servername, $username, $password, "");
$connBloodPressure = new mysqli($servername, $username, $password, "");
$connHeartRate = new mysqli($servername, $username, $password, "");
$connCholesterolLdl = new mysqli($servername, $username, $password, "");
$connCholesterolHdl = new mysqli($servername, $username, $password, "");

// Function to get the latest sensor data
function getLatestSensorData($conn, $table, $userId, $name, $valueField, $unit, $statusField, $timestampField) {
    $tooOldThreshold = strtotime('-6 day');

    $sql = "SELECT $valueField AS value, $statusField AS status, $timestampField AS last_updated 
            FROM $table 
            WHERE user_id = $userId
            ORDER BY $timestampField DESC 
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastUpdatedTimestamp = strtotime($row['last_updated']);

        if ($lastUpdatedTimestamp < $tooOldThreshold) {
            return [
                "id" => $userId,
                "name" => $name,
                "value" => $row['value'],
                "unit" => $unit,
                "status" => "gray",
                "last_updated" => date('d M Y h:i:s A', $lastUpdatedTimestamp)
            ];
        } else {
            return [
                "id" => $userId,
                "name" => $name,
                "value" => $row['value'],
                "unit" => $unit,
                "status" => $row['status'],
                "last_updated" => date('d M Y h:i:s A', $lastUpdatedTimestamp)
            ];
        }
    } else {
        return [
            "id" => $userId,
            "name" => $name,
            "value" => "N/A",
            "unit" => $unit,
            "status" => "white",
            "last_updated" => "N/A"
        ];
    }
}

// Function to get the latest blood pressure data (systolic/diastolic)
function getLatestBloodPressureData($conn, $table, $userId) {
    $tooOldThreshold = strtotime('-6 day');

    $sql = "SELECT systolic, diastolic, systolic_status, diastolic_status, recorded_at 
            FROM $table 
            WHERE user_id = $userId
            ORDER BY recorded_at DESC 
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastUpdatedTimestamp = strtotime($row['recorded_at']);

        if ($lastUpdatedTimestamp < $tooOldThreshold) {
            return [
                "id" => $userId,
                "name" => "Blood Pressure",
                "value" => $row['systolic'] . "/" . $row['diastolic'],
                "unit" => "mmHg",
                "systolic_status" => "gray",
                "diastolic_status" => "gray",
                "last_updated" => date('d M Y h:i:s A', $lastUpdatedTimestamp)
            ];
        } else {
            return [
                "id" => $userId,
                "name" => "Blood Pressure",
                "value" => $row['systolic'] . "/" . $row['diastolic'],
                "unit" => "mmHg",
                "systolic_status" => $row['systolic_status'],
                "diastolic_status" => $row['diastolic_status'],
                "last_updated" => date('d M Y h:i:s A', $lastUpdatedTimestamp)
            ];
        }
    } else {
        return [
            "id" => $userId,
            "name" => "Blood Pressure",
            "value" => "N/A",
            "unit" => "mmHg",
            "systolic_status" => "white",
            "diastolic_status" => "white",
            "last_updated" => "N/A"
        ];
    }
}

// Array to hold sensor data
$sensorData = [];

// Get data for each sensor type
$sensorData[] = getLatestSensorData($connWeight, "weight$id", $id, 'Weight', 'weight_value', 'lbs.', 'status', 'recorded_at');
$sensorData[] = getLatestSensorData($connTemperature, "temperature$id", $id, 'Temperature', 'temperature_value', 'F', 'status', 'recorded_at');
$sensorData[] = getLatestSensorData($connGlucose, "glucose$id", $id, 'Glucose', 'glucose_value', 'mg/dl', 'status', 'recorded_at');
$sensorData[] = getLatestSensorData($connBloodOxygen, "blood_oxygen$id", $id, 'Blood Oxygen', 'blood_oxygen_value', 'SpO2%', 'status', 'recorded_at');
$sensorData[] = getLatestBloodPressureData($connBloodPressure, "blood_pressure$id", $id);
$sensorData[] = getLatestSensorData($connHeartRate, "heart_rate$id", $id, 'Heart Rate', 'heart_rate_value', 'bpm', 'status', 'recorded_at');
$sensorData[] = getLatestSensorData($connCholesterolLdl, "cholesterol_ldl$id", $id, 'Cholesterol (LDL)', 'cholesterol_ldl_value', 'mg/dl', 'status', 'recorded_at');
$sensorData[] = getLatestSensorData($connCholesterolHdl, "cholesterol_hdl$id", $id, 'Cholesterol (HDL)', 'cholesterol_hdl_value', 'mg/dl', 'status', 'recorded_at');

// Output the JSON response
echo json_encode($sensorData);

// Close the connections
$connWeight->close();
$connTemperature->close();
$connGlucose->close();
$connBloodOxygen->close();
$connBloodPressure->close();
$connHeartRate->close();
$connCholesterolLdl->close();
$connCholesterolHdl->close();
?>
