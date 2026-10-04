<?php 
require_once("includes/config.php");

if (!empty($_POST["studentid"])) {
    $search = trim($_POST["studentid"]);

    $sql = "SELECT id, StudentId, FullName, EmailId, MobileNumber, Status 
            FROM tblstudents 
            WHERE StudentId = :search OR FullName LIKE :searchLike";
    $query = $dbh->prepare($sql);
    $query->bindValue(':search', $search, PDO::PARAM_STR);
    $query->bindValue(':searchLike', '%' . $search . '%', PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 1) {
        // Multiple matches found: display selectable list
        echo '<div class="alert alert-info border-info-subtle p-3 rounded-3 mt-2">';
        echo '<div class="d-flex align-items-center mb-2"><i class="bi bi-people-fill me-2 fs-5"></i><span class="fw-bold small">Multiple students found. Please click to select:</span></div>';
        echo '<div class="list-group list-group-flush">';
        foreach ($results as $row) {
            $statusBadge = ($row->Status == 1) 
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>'
                : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Blocked</span>';

            echo '<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 rounded-2 mb-1 border" onclick="selectStudent(\'' . htmlspecialchars($row->StudentId) . '\', \'' . htmlspecialchars(addslashes($row->FullName)) . '\')">';
            echo '<div><strong class="text-dark">' . htmlspecialchars($row->FullName) . '</strong> <span class="text-muted small">(' . htmlspecialchars($row->StudentId) . ')</span><br><small class="text-muted">' . htmlspecialchars($row->EmailId) . '</small></div>';
            echo $statusBadge;
            echo '</button>';
        }
        echo '</div></div>';
        echo "<script>$('#submit').prop('disabled', true); $('#studentid').val('');</script>";

    } elseif ($query->rowCount() == 1) {
        // Single match found
        $result = $results[0];
        if ($result->Status == 0) {
            echo '<div class="alert alert-danger d-flex align-items-center p-3 rounded-3 mt-2" role="alert">';
            echo '<i class="bi bi-x-circle-fill fs-5 me-2 flex-shrink-0"></i>';
            echo '<div><strong>Student Blocked:</strong> ' . htmlspecialchars($result->FullName) . ' (' . htmlspecialchars($result->StudentId) . ') account is suspended.</div>';
            echo '</div>';
            echo "<script>$('#submit').prop('disabled', true); $('#studentid').val('');</script>";
        } else {
            echo '<div class="alert alert-success d-flex align-items-center justify-content-between p-3 rounded-3 mt-2" role="alert">';
            echo '<div class="d-flex align-items-center gap-3">';
            echo '<div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-person-check fs-5"></i></div>';
            echo '<div>';
            echo '<strong class="text-dark d-block">' . htmlspecialchars($result->FullName) . '</strong>';
            echo '<span class="text-secondary small me-2"><i class="bi bi-vcard me-1"></i>' . htmlspecialchars($result->StudentId) . '</span>';
            echo '<span class="text-secondary small"><i class="bi bi-telephone me-1"></i>' . htmlspecialchars($result->MobileNumber) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">Active Student</span>';
            echo '</div>';
            echo "<script>$('#submit').prop('disabled', false); $('#studentid').val('" . htmlspecialchars($result->StudentId) . "');</script>";
        }
    } else {
        // No match found
        echo '<div class="alert alert-danger d-flex align-items-center p-3 rounded-3 mt-2" role="alert">';
        echo '<i class="bi bi-exclamation-triangle-fill fs-5 me-2 flex-shrink-0"></i>';
        echo '<div><strong>Not Found:</strong> No registered student matched "<em>' . htmlspecialchars($search) . '</em>".</div>';
        echo '</div>';
        echo "<script>$('#submit').prop('disabled', true); $('#studentid').val('');</script>";
    }
}
?>