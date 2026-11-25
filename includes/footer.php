<?php
// Shared page footer for authenticated USIAMS pages.

if (PHP_SAPI !== 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}
?>
    </main>
    <footer class="site-footer">
        University Student Information and Academic Management System (USIAMS)
    </footer>
</body>
</html>
