<?php
  // JSON
  header('Content-Type: application/json');

  // mengijinkan semua host/domain/ip untuk menggunakan data JSON ini bila menggunakan AJAX
  // atau rubah tanda * menjadi domain yg di tentukan
  header('Access-Control-Allow-Origin: *');

  $url = 'https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/COVID19_Indonesia_per_Provinsi/FeatureServer/0/query?where=1%3D1&outFields=*&outSR=4326&f=json';
  $content = file_get_contents($url);
  $array = json_decode($content, true);
  $array = $array['features'];

  foreach ( $array as $k=>$v ) {
    if ($array[$k]['attributes']['Provinsi'] != 'Indonesia') {
      $array[$k] ['Provinsi'] = $array[$k]['attributes']['Provinsi'];
      $array[$k] ['Kasus Positif'] = $array[$k]['attributes']['Kasus_Posi'];
      $array[$k] ['Kasus Sembuh'] = $array[$k]['attributes']['Kasus_Semb'];
      $array[$k] ['Kasus Meninggal'] = $array[$k]['attributes']['Kasus_Meni'];
    }
    else {
      unset ($array[$k]);
    }
    unset($array[$k]['attributes']);
    unset($array[$k]['geometry']);
  }

  $flag = 0;
  foreach (explode ("/", $_SERVER['REQUEST_URI']) as $part) {
    $flag++;
    if ($flag == 4 && strlen($part) > 0) {  //Ganti sesuai dengan URL
      foreach ( $array as $k=>$v ) {
        if (strtolower($array[$k] ['Provinsi']) != strtolower(urldecode($part))) {
          unset ($array[$k]);
        }
      }
      $array = array_shift($array);
    }
    if ($flag > 4 && strlen($part) > 0) { //Ganti sesuai dengan URL, jangan diberi klo lebih, gibang aja
      unset ($array);
    }
  }

  if (!$array) {
    echo json_encode (array("404"=>"Data not found"), JSON_PRETTY_PRINT);
  }
  else {
    echo json_encode ($array, JSON_PRETTY_PRINT);
  }
  //print_r ($array);
?>
