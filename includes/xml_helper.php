<?php
// Helper for atomic regeneration of students.xml from MySQL using DOMDocument.

/**
 * Append a child element with an automatically escaped text node.
 */
function append_xml_text_element(DOMDocument $dom, DOMElement $parent, string $tag, string $value): void
{
    $element = $dom->createElement($tag);
    $element->appendChild($dom->createTextNode($value));
    $parent->appendChild($element);
}

/**
 * Regenerate students.xml from MySQL students table using an atomic temp-file rename.
 *
 * @param mysqli      $conn     Active mysqli connection.
 * @param string|null $xml_path Optional custom target file path (defaults to project students.xml).
 * @return bool True on success, false on query or filesystem failure.
 */
function regenerate_students_xml(mysqli $conn, ?string $xml_path = null): bool
{
    require_once __DIR__ . '/db.php';

    $target_path = $xml_path ?? dirname(__DIR__) . DIRECTORY_SEPARATOR . 'students.xml';
    $target_dir = dirname($target_path);

    if (!is_dir($target_dir) || !is_writable($target_dir)) {
        error_log('USIAMS XML error: target directory is not writable: ' . $target_dir);
        return false;
    }

    $result = execute_prepared(
        $conn,
        'SELECT reg_no, student_name, course, semester, email, contact_no FROM students ORDER BY reg_no ASC'
    );

    if ($result === false) {
        error_log('USIAMS XML error: failed to query students table.');
        return false;
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;

    $root = $dom->createElement('students');
    $dom->appendChild($root);

    while ($row = $result->fetch_assoc()) {
        $student_node = $dom->createElement('student');
        append_xml_text_element($dom, $student_node, 'regno', (string) $row['reg_no']);
        append_xml_text_element($dom, $student_node, 'name', (string) $row['student_name']);
        append_xml_text_element($dom, $student_node, 'course', (string) $row['course']);
        append_xml_text_element($dom, $student_node, 'semester', (string) $row['semester']);
        append_xml_text_element($dom, $student_node, 'email', (string) $row['email']);
        append_xml_text_element($dom, $student_node, 'contact', (string) $row['contact_no']);
        $root->appendChild($student_node);
    }
    $result->free();

    $xml_content = $dom->saveXML();
    if ($xml_content === false) {
        error_log('USIAMS XML error: DOMDocument::saveXML() failed.');
        return false;
    }

    try {
        $tmp_suffix = bin2hex(random_bytes(6));
    } catch (Throwable $e) {
        $tmp_suffix = (string) getmypid();
    }

    $tmp_path = $target_dir . DIRECTORY_SEPARATOR . '.students_xml_' . $tmp_suffix . '.tmp';

    if (@file_put_contents($tmp_path, $xml_content, LOCK_EX) === false) {
        error_log('USIAMS XML error: failed to write temporary XML file: ' . $tmp_path);
        @unlink($tmp_path);
        return false;
    }

    if (!@rename($tmp_path, $target_path)) {
        error_log('USIAMS XML error: failed to atomically replace target XML file: ' . $target_path);
        @unlink($tmp_path);
        return false;
    }

    return true;
}
