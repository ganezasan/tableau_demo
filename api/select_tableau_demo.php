<?php
    require_once('config.php');
    $username = DB_USERNAME; 
    $password = DB_PASSWORD;
    $host = DB_HOST;
    $database=DATABASE;
    $port = PORT;
    $table="tableau_demo";
    $db_type = "redshift";

    $server = pg_connect("host={$host} port={$port} dbname={$database} user={$username} password={$password}");

    $query = "SELECT product_subcategory,supplier,sum(qty) as qty,sum(amt) as amt,sum(sendenhi) as sendenhi  FROM tableau_demo group by product_subcategory,supplier order by product_subcategory,supplier";

    $data = array();
    if ($result = pg_query($server,$query)) {
        $dataLength = pg_num_rows($result);
        $values = array();
        $tmp = pg_fetch_assoc($result);

        $tmpKey = $tmp["product_subcategory"];
        for ($x = 0; $x < $dataLength; $x++) {
            if($tmpKey === $tmp["product_subcategory"]){
                $values[] = array(
                            "size" => intval($tmp["qty"]),
                            "x"    => intval($tmp["sendenhi"]),
                            "y"    => intval($tmp["amt"])
                );
                $tmp = pg_fetch_assoc($result);
            }else{
                //データを入れる
                $data[] = array(
                    "key" => $tmpKey,
                    "values" => $values
                );
                //データの初期化
                $tmpKey = $tmp["product_subcategory"];
                $values = array();
                $x--;
            }
        }
        $data[] = array(
            "key" => $tmpKey,
            "values" => $values
        );
    }

    // var_dump($query);

    header('Content-type:application/json; charset=UTF-8');
    echo json_encode($data);     
    pg_close($server);

?>
