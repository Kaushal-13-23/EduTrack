<?php
// Protected page to view student records loaded from students.xml.

require_once __DIR__ . '/includes/auth.php';
require_login();

$xml_path = __DIR__ . DIRECTORY_SEPARATOR . 'students.xml';
$records = [];
$status_message = '';

if (!file_exists($xml_path) || !is_readable($xml_path)) {
    $status_message = 'XML records file is missing. Add, edit, or delete a student to generate it.';
} else {
    $raw_content = @file_get_contents($xml_path);
    if ($raw_content === false || trim($raw_content) === '') {
        $status_message = 'XML records file is empty.';
    } else {
        $prev_libxml = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($xml_path);
        libxml_clear_errors();
        libxml_use_internal_errors($prev_libxml);

        if ($xml === false) {
            $status_message = 'Unable to read XML records right now.';
        } else {
            foreach ($xml->student as $student) {
                $records[] = [
                    'regno'    => (string) ($student->regno ?? ''),
                    'name'     => (string) ($student->name ?? ''),
                    'course'   => (string) ($student->course ?? ''),
                    'semester' => (string) ($student->semester ?? ''),
                    'email'    => (string) ($student->email ?? ''),
                    'contact'  => (string) ($student->contact ?? ''),
                ];
            }
        }
    }
}

$page_title = 'XML Student Records - USIAMS';
$active_page = 'xml_view.php';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
    <div class="page-header">
        <h1>XML Student Records</h1>
        <div class="form-actions">
            <a class="btn btn-secondary" href="dashboard.php">Back to Dashboard</a>
            <a class="btn btn-secondary" href="view_students.php">View Students</a>
        </div>
    </div>

    <?php if ($status_message !== ''): ?>
        <p class="alert alert-error" role="alert"><?php echo esc($status_message); ?></p>
    <?php else: ?>
        <p class="record-count"><strong>Total Records:</strong> <?php echo count($records); ?></p>

        <?php if (empty($records)): ?>
            <p class="empty-state">No student records found in XML.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Email</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $rec): ?>
                            <tr>
                                <td><?php echo esc($rec['regno']); ?></td>
                                <td><?php echo esc($rec['name']); ?></td>
                                <td><?php echo esc($rec['course']); ?></td>
                                <td><?php echo esc($rec['semester']); ?></td>
                                <td><?php echo esc($rec['email']); ?></td>
                                <td><?php echo esc($rec['contact']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
