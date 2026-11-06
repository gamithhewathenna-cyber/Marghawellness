<?php
declare(strict_types=1);

/** CSV download for enquiries and subscribers (opens in Excel / Google Sheets). */
require __DIR__ . '/_inc/bootstrap.php';
require_admin();

$ent = admin_entity((string) ($_GET['type'] ?? ''));
if (!$ent || empty($ent['export'])) {
    flash('That list can’t be exported.', 'error');
    redirect(admin_url());
}
require_tables([$ent['table']]);

$stmt = db()->query('SELECT * FROM `' . $ent['table'] . '` ORDER BY ' . $ent['order']);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="margha-' . $ent['key'] . '-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows ā, ’ etc. correctly
$first = true;
while ($row = $stmt->fetch()) {
    unset($row['ip_address']);
    if ($first) {
        fputcsv($out, array_keys($row));
        $first = false;
    }
    // Prefix values that spreadsheets would treat as formulas.
    fputcsv($out, array_map(fn($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v, $row));
}
if ($first) {
    fputcsv($out, ['No records yet']);
}
fclose($out);
