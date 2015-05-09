<?php

function get_products_recent() {
    require(ROOT_PATH."inc/database.php");
   
    try {

        $results = $db->query("SELECT poster
                              FROM posts
                              ");
    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $recent = $results->fetchAll(PDO::FETCH_ASSOC);
    $recent = array_reverse($recent);

    return $recent;
}

?>