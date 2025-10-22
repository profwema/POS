<?php

// دوال مساعدة للاستعلامات المجهزة (MySQLi Prepared)

function db_prepare_and_execute($mysqli, $sql, $types = '', $params = [])
{
  $stmt = mysqli_prepare($mysqli, $sql);
  if (!$stmt) {
    return [false, null];
  }
  if ($types !== '' && !empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
  }
  if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    return [false, null];
  }
  return [true, $stmt];
}

function db_fetch_all_assoc($stmt)
{
  $result = mysqli_stmt_get_result($stmt);
  if ($result === false) return [];
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

