<?php
function q($conn, $sql, $params = [])
{
  $stmt = $conn->prepare($sql);
  if ($params) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  return $stmt->get_result();
}
