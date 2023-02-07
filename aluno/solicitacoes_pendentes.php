<?php
    include_once("../conexao.php");
    session_start();
    $matricula = $_SESSION['login'];
if($_SESSION['auth'] && $_SESSION['login']!==""){
    $sql = "SELECT * FROM wp_solicitations WHERE resultado='PENDENTE' AND matricula='$matricula' ORDER BY solicitation_id DESC";
    $consulta = mysqli_query($conexao, $sql);
    $solicitacoes = mysqli_num_rows($consulta);
}else{
    print "<div class='alert alert-warning' role='alert'>Faça Login.</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <meta charset="utf-8"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/solicitacoes.css">
    <link rel="icon" href="../icon.png">
    <title> Solicitações Pendentes</title>
</head>
<body>
    <?php
        include('./header.php');

        if($solicitacoes == 0){
            print "<div class='alert alert-warning' role='alert'>Não há solicitações pendentes.</div>";
        }else{
         while($solicitacao=mysqli_fetch_array($consulta)){
            $id = $solicitacao[0];
            $atividade = $solicitacao[8];
            $grupo = $solicitacao[10];
            $semestre = $solicitacao[3]; 
            $carga_horaria = $solicitacao[9];
            $documento = $solicitacao[12];

            print "<div class='card' id='card'>";
                print "<div class='card-body'>";
                    print "<div class='container' style='padding:0'>";
                        print"<div class='row' style='padding:0'>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Código</h> </div>";
                            print"<div class='col-3' ><h style='color:gray; font-size:18px; font-weight:bold'>Atividade</h> </div>";
                            print"<div class='col-2'><h style='color:gray; font-size:18px; font-weight:bold'>Grupo Solicitado</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Semestre</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Carga Horaria Solicitada</h> </div>";
                            print "<div class='col'></div>";
                            print "<div class='col'></div>";

                        print"</div>";
                        print"<div class='row' style='padding:0'>";
                            print "<div  class='col' name='id_solicitation' id='id_solicitation'>$id</div>";
                            print "<div class='col-3' >$atividade</div>";
                            print "<div class='col-2'>$grupo</div>";
                            print "<div class='col'>$semestre</div>";
                            print "<div class='col'>$carga_horaria</div>";
                            print "<div data-idsolicitation='$id' class='col' id='edit'>";
                            print "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='green' class='bi bi-pencil' viewBox='0 0 16 16'>";
                            print "<path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z'/>";
                            print " </svg>";
                            print "</div>";
                            print "<div data-idsolicitation='$id' class='col' id='trash'>";
                            print "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='green' class='bi bi-trash' viewBox='0 0 16 16'>";
                            print "<path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/>";
                            print "<path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/>";
                            print "</svg>";
                            print "</div>";
                        print"</div>";
                    print"</div>";
                print"</div>";
            print"</div>";
        }
    }

        mysqli_close($conexao);
    ?>

<script>

    $(document).ready(function(){
        $('#edit').css({'cursor': 'pointer'});
        $(document).on("click", "#edit", function(e){
            e.preventDefault();
            var soli_id = $(this).attr("data-idsolicitation");
            $.post("backend.php", {"codigo": soli_id});
            window.location.href ="solicitation_details.php";
        });
        $('#trash').css({'cursor': 'pointer'});
        $(document).on("click", "#trash", function(e){
            e.preventDefault();
            var soli_id = $(this).attr("data-idsolicitation");
            $.post("delete.php", {"codigo": soli_id})
            .always(function() {
                window.location.reload();
            });
        });
    })
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>

</body>
</html>