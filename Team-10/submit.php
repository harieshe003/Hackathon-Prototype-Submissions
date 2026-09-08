<?php
// api/verification/submit.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Invalid request method'], 405);
}

requireBusiness();
$user = getCurrentUser();

$productId = intval($_POST['product_id'] ?? 0);
$docName = sanitize($_POST['document_name'] ?? 'LCA Report');
$sourceUrl = sanitize($_POST['source_url'] ?? '');
$notes = sanitize($_POST['notes'] ?? '');

if ($productId <= 0) {
    jsonResponse(['success' => false, 'error' => 'Product ID is required'], 400);
}

$db = Database::getInstance();
// Find business ID for current user
$stmtBiz = $db->prepare("SELECT id FROM businesses WHERE user_id = ? LIMIT 1");
$stmtBiz->execute([$user['id']]);
$biz = $stmtBiz->fetch();

$bizId = $biz['id'] ?? 1;

$stmtReq = $db->prepare("INSERT INTO verification_requests (product_id, business_id, status, reviewer_notes) VALUES (?, ?, 'Pending', ?)");
$stmtReq->execute([$productId, $bizId, 'Submitted by business representative for ISO 14040 verification review.']);
$reqId = (int)$db->lastInsertId();

$stmtEv = $db->prepare("INSERT INTO verification_evidence (verification_id, document_name, file_path, source_url, notes) VALUES (?, ?, ?, ?, ?)");
$stmtEv->execute([$reqId, $docName, 'uploads/verification_doc_' . $reqId . '.pdf', $sourceUrl, $notes]);

jsonResponse([
    'success' => true,
    'message' => 'Verification request submitted successfully. EcoLens auditors will review your evidence.',
    'request_id' => $reqId
]);
