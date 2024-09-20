<?php

ini_set('memory_limit', '-1');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$list = file_get_contents(__DIR__ . '/data/is_officer_sept_2024.json');
$list = json_decode($list, true);
// 46,682
// print_r(['total_raw' => count($list)]);

$odds = [];
$even = [];
$non_occurence = [];

$males = array_keys(array_column($list, 'sex_at_birth'), 'MALE');
$females = array_keys(array_column($list, 'sex_at_birth'), 'FEMALE');
// 46,677
// print_r(['males' => count($males), 'females' => count($females)]);

$find_nationality = function($needle) {
  $nationalities = file_get_contents(__DIR__ . '/data/nationalities.json');
  $nationalities = json_decode($nationalities, true);
  $key = array_search($needle, array_column($nationalities, 'country'));
  return $nationalities[$key]['nationality'];
};

for ($i = 0; $i <= count($females); $i++) {
  $item = $list[$i];
  try {
    $is_even = $item['id'] % 2 == 0;

    if ($is_even) {
      $even[$i] = $item;
      $even[$i]['occurence'] = "Even - ".$item['id'];
      $even[$i]['date_reported'] = date('F j, Y', strtotime($item['date_reported']));
      if (isset($item['nationality_country'])) {
        $even[$i]['nationality'] = $find_nationality($item['nationality_country']);
      }
    } else {
      array_push($non_occurence, $item);
    }
  } catch (\Exception $e) {
    print_r($e->getMessage());
  }
}

for ($i = 0; $i <= count($males); $i++) {
  $item = $list[$i];
  try {
    $is_even = $item['id'] % 2 == 0;

    if (!$is_even) {
      $odds[$i] = $item;
      $odds[$i]['occurence'] = "Odd - ".$item['id'];
      $odds[$i]['date_reported'] = date('F j, Y', strtotime($item['date_reported']));
      if (isset($item['nationality_country'])) {
        $odds[$i]['nationality'] = $find_nationality($item['nationality_country']);
      }
    } else {
      array_push($non_occurence, $item);
    }
  } catch (\Exception $e) {
    print_r($e->getMessage());
  }
}
//46,682
//46,677
//46,679 
// print_r(['males' => count($odds), 'females' => count($even), 'non_occurence' => count($non_occurence)]);

$new_list = array_merge($odds, $even, $non_occurence);

$offset = 0;
$limit = 10;
$new_list = array_slice($new_list, $offset, $limit); 

echo json_encode(['data' => $new_list]);