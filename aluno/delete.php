<?php
    $codigo = $_POST['codigo'];
    
    include_once("../conexao.php");

     $sql = "DELETE FROM wp_solicitations WHERE solicitation_id='$codigo'";
     $deletar = mysqli_query($conexao, $sql);
    
     echo "FUNÇÃO DE DELETAR";
?>