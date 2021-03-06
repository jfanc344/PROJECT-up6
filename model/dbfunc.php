<?php
function insertData($table, $data){
  GLOBAL $conn;
  if(!empty($data) && is_array($data)){
    $columns = '';
    $values = '';
    $i = 0;
    $columnString = implode(',', array_keys($data));
    $valueString = ":".implode(',:', array_keys($data));
    $sql = "INSERT INTO ".$table." (".$columnString.") VALUES (".$valueString.")";
    $query = $conn->prepare($sql);
    print_r($data);
    foreach ($data as $key => $val) {
        $query->bindValue(':'.$key, $val);
    }
      $insert = $query->execute();
  }
  return $insert;
}

function test_user_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


function selectData($table, $conditions = array()){
  GLOBAL $conn;
  $sql = 'SELECT';
  //select column list or * (all)
  $sql .= array_key_exists("select", $conditions)?$conditions['select']:'*';
  $sql .= 'FROM '.$table;
  //where conditions - if any
  if(array_key_exists("where", $conditions)){
    $sql .= ' WHERE ';
    $i = 0;
    foreach($conditions['where'] as $key => $value){
       $pre = ($i > 0)?' AND ':'';
       $sql .= $pre.$key." = '".$value."'";
       $i++;

     }
  }
  //order by column/s
  if(array_key_exists("order_by",$conditions)){
    $sql .= ' ORDER BY '.$conditions['order_by'];
  }
  //limit conditions - if any
  if(array_key_exists("start",$conditions) && array_key_exists("limit", $conditions)) {
    $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];
  }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
    $sql .= ' LIMIT '.$conditions['limit'];
  }
  $query = $conn->prepare($sql);
  $data= $query->execute();

//return single rows or all rows - identification fetch function required
 if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
     switch($conditions['return_type']){
       case 'count':
           $data = $query->rowCount();
           break;
       case 'single':
           $data = $query->fetch(PDO::FETCH_ASSOC);
           break;
       default:
           $data = '';
     }
 }else{

    if($query->rowCount() > 0){
        $data = $query->fetchAll();
    }
    return $data;
  }
 return $data;
}


function deleteData($table, $conditions){
  GLOBAL $conn;
  $whereSql = '';
  if(!empty($conditions)&& is_array($conditions)){
    $whereSql .= ' WHERE ';
    $i = 0;
    foreach($conditions as $key => $value){
      $pre = ($i > 0)?' AND ':'';
      $whereSql .= $pre.$key. " = '".$value."'";
      $i++;
    }
    $delete = $conn->exec("DELETE FROM ".$table.$whereSql);
    //return $deleted;
  }
  return $deleted;
}



?>
