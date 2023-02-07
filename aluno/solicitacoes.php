<?php

    include_once("../conexao.php");

    session_start();
    $matricula = $_SESSION['login'];

    $filtro = isset($_POST['filtro'])?$_POST['filtro']:"";

if($_SESSION['auth'] && $_SESSION['login']!==""){

    if($filtro==0){
        $sql = "SELECT * FROM wp_solicitations WHERE matricula='$matricula' AND resultado!='PENDENTE' ORDER BY solicitation_id DESC";
    }else{
        $sql = "SELECT * FROM wp_solicitations WHERE matricula='$matricula' AND semestre='$filtro' AND resultado!='PENDENTE' ORDER BY solicitation_id DESC";
    }
    $consulta = mysqli_query($conexao, $sql);
    $solicitacoes = mysqli_num_rows($consulta);
}else{
    print "<div class='alert alert-warning' role='alert'>Faça Login.</div>";
}

?>
<!DOCTYPE html>
<html>
    <meta charset="utf-8"/>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/solicitacoes.css">
    <link rel="icon" href="../icon.png">
    <title>Meus Resultados</title>
</head>
<body>
    <?php
        include('./header.php')
    ?>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end" style='margin-right: 20px; margin-top:20px; margin-bottom: 40px'>   
        <div id="label" style='margin-top:5px'>Filtro</div>
         <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post"
            style='display:flex; flex-direction:row'
         >
            <div  id="select">
                <select  class="form-select" name="filtro" id="filtro">
                    <option value="0" selected>Selecione um semestre</option>
                    <option value="2020/1">2020/1</option>
                    <option value="2020/2">2020/2</option>
                    <option value="2021/1">2021/1</option>
                </select>
                <input type="hidden" name="matricula" value="<?php echo $matricula ?>">
            </div>
            <button  type="submit" 
            style='background-color:white; border:0; border-radius:5px; margin-left: 10px'>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-funnel" viewBox="0 0 16 16">
                <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z"/>
            </svg>
            </button>
        </form>   
    </div>
    <?php   
        if($solicitacoes == 0){
            print "<div class='alert alert-warning' role='alert'> Não há solicitações analisadas ainda.</div>";
        }else{
         while($solicitacao=mysqli_fetch_array($consulta)){
            $id = $solicitacao[0];
            $atividade = $solicitacao[8];
            $grupo = $solicitacao[10];
            $semestre = $solicitacao[3]; 
            $resultado = $solicitacao[13];
            $motivo = $solicitacao[14];
            $carga_horaria = $solicitacao[15];
            $documento = $solicitacao[12];

            if($carga_horaria==0 && ($resultado=='Deferido' || $resultado=='DEFERIDO' || $resultado=='deferido') ){
                $carga_horaria=$solicitacao[9];
            }
            print "<div class='card' id='card'>";
                print "<div class='card-body'>";
                    print "<div class='container' style='padding:0'>";
                        print"<div class='row' style='padding:0'>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Código</h> </div>";
                            print"<div class='col-3' ><h style='color:gray; font-size:18px; font-weight:bold'>Atividade</h> </div>";
                            print"<div class='col-2'><h style='color:gray; font-size:18px; font-weight:bold'>Grupo Solicitado</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Semestre</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Resultado</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Motivo</h> </div>";
                            print"<div class='col'><h style='color:gray; font-size:18px; font-weight:bold'>Carga Horaria Adquirida</h> </div>";
                        print"</div>";
                        print"<div class='row' style='padding:0'>";
                            print "<a class='col' href='$documento' target='_blank'>$id</a>";
                            print "<div class='col-3' >$atividade</div>";
                            print "<div class='col-2'>$grupo</div>";
                            print "<div class='col'>$semestre</div>";
                            print "<div class='col'>$resultado</div>";
                            print "<div class='col'>$motivo</div>";
                            print "<div class='col'>$carga_horaria</div>";
                        print"</div>";
                    print"</div>";
                print"</div>";
            print"</div>";
        }
    }

        mysqli_close($conexao);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
</body>
</html>
