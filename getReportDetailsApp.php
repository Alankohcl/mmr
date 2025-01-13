<?php
include 'database.php';

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['report_id'])) {
        $report_id = intval($_GET['report_id']);

        $sql = "
        SELECT 
            mr.report_id,
            mr.remarks,
            mr.created_at AS report_created_at,
            
            -- Patient Details
            p.user_id AS patient_id,
            p.name AS patient_name,
            p.email AS patient_email,
            p.phone_number AS patient_phone,
            
            -- Lab Assistant Details
            la.user_id AS lab_assistant_id,
            la.name AS lab_assistant_name,
            
            -- Doctor Details
            d.user_id AS doctor_id,
            d.name AS doctor_name,
            
            -- Blood Test Details
            bt.haemoglobin_level,
            bt.platelet_count,
            bt.neutrophils_percent,
            bt.lymphocytes_percent,
            bt.monocytes_percent,
            bt.eosinophils_percent,
            bt.basophils_percent,
            
            -- Health Metric Details
            hm.blood_pressure,
            hm.body_mass_index,
            hm.hemoglobin_a1c,
            hm.pulse_rate,
            hm.random_blood_sugar
        FROM 
            medical_reports mr
        LEFT JOIN 
            users p ON mr.patient_id = p.user_id
        LEFT JOIN 
            users la ON mr.lab_assistant_id = la.user_id
        LEFT JOIN 
            users d ON mr.doctor_id = d.user_id
        LEFT JOIN 
            blood_tests bt ON mr.blood_test_id = bt.blood_test_id
        LEFT JOIN 
            health_metrics hm ON mr.health_metric_id = hm.health_metric_id
        WHERE 
            mr.report_id = ?;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $report_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                echo json_encode([
                    "success" => true,
                    "report_details" => [
                        "report_id" => $data['report_id'],
                        "patient_id" => $data['patient_id'],
                        "lab_assistant_id" => $data['lab_assistant_id'],
                        "doctor_id" => $data['doctor_id'],
                        "remarks" => $data['remarks'],
                        "report_created_at" => $data['report_created_at'],
                        "bloodTest" => [
                            "haemoglobin_level" => $data['haemoglobin_level'],
                            "platelet_count" => $data['platelet_count'],
                            "neutrophils_percent" => $data['neutrophils_percent'],
                            "lymphocytes_percent" => $data['lymphocytes_percent'],
                            "monocytes_percent" => $data['monocytes_percent'],
                            "eosinophils_percent" => $data['eosinophils_percent'],
                            "basophils_percent" => $data['basophils_percent']
                        ],
                        "healthMetric" => [
                            "blood_pressure" => $data['blood_pressure'],
                            "body_mass_index" => $data['body_mass_index'],
                            "hemoglobin_a1c" => $data['hemoglobin_a1c'],
                            "pulse_rate" => $data['pulse_rate'],
                            "random_blood_sugar" => $data['random_blood_sugar']
                        ]
                    ]
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No report found for the given report_id"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Failed to execute query"
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "report_id is required"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
}

$conn->close();
?>
