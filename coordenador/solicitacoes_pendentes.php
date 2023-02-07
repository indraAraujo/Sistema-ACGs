<?php
    include_once("../conexao.php");
    
    $sql = "SELECT * FROM wp_solicitations WHERE resultado='PENDENTE'";
    $consulta = mysqli_query($conexao, $sql);
    $solicitacoes = mysqli_num_rows($consulta);
    
    session_start();
  
    
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <meta charset="utf-8"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/solicitacoes.css">
    <link rel="icon" href="../icon.png">
    <title>Todas Solicitações</title>
</head>
<body>
    <?php
        include('./header.php');

        if($solicitacoes == 0){
            print "<div class='alert alert-warning' role='alert'>Não há solicitações cadastradas.</div>";
        }else{
         while($solicitacao=mysqli_fetch_array($consulta)){
            $id = $solicitacao[0];
            $atividade = $solicitacao[8];
            $grupo = $solicitacao[10];
            $semestre = $solicitacao[3]; 
            $resultado = $solicitacao[13];
            $motivo = $solicitacao[14];
            $carga_horaria = $solicitacao[9];
            $nome = $solicitacao[5];

            print "<div class='card' id='card' >";
                print "<div class='card-body' style='padding:0'>";
                    print "<div class='container' style='padding:0'>";
                    print "<form method='post' action='solicitation_details'>";
                        print"<div class='row' style='padding:0'>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Código</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Aluno</h> </div>";
                            print"<div class='col-3' ><h style='color:gray; font-size:18px; font-weight:bold'>Atividade</h> </div>";
                            print"<div class='col-2'><h style='color:gray; font-size:18px; font-weight:bold'>Grupo Solicitado</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Semestre</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Resultado</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Motivo</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Carga Horaria Solicitada</h> </div>";
                        print"</div>";
                        print"<div class='row' style='padding:0'>";
                            print "<div class='col' >";
                            print "<div data-idsolicitation='$id' name='id_solicitation' id='id_solicitation' style='background-color:lightgreen; width:50%; text-align:center; border-radius:20px; font-weight:bold'>";
                            print "$id";
                            print "</div>";
                            print "</div>";
                            print "<div class='col'>$nome</div>";
                            print "<div class='col-3' >$atividade</div>";
                            print "<div class='col-2'>$grupo</div>";
                            print "<div class='col'>$semestre</div>";
                            print "<div class='col'>$resultado</div>";
                            print "<div class='col'>$motivo</div>";
                            print "<div class='col'>$carga_horaria</div>";
                        print"</div>";
                        print "</form>";
                    print"</div>";
                print"</div>";
            print"</div>";
        }
    }


        mysqli_close($conexao);
    ?>


<script>
    $(document).ready(function(){
        $('#id_solicitation').css({'cursor': 'pointer'});
        $(document).on("click", "#id_solicitation", function(e){
            e.preventDefault();
            var soli_id = $(this).attr("data-idsolicitation");
            $.post("backend.php", {"codigo": soli_id});
            window.location.href ="solicitation_details.php";
        })
    })
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>

</body>
</html>