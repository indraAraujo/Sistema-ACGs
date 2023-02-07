<?php
    session_start();
    include_once("conexao.php");

    $usuario = isset( $_POST['usuario']) ? $_POST['usuario'] : ""; 
    $login = isset( $_POST['login']) ? $_POST['login'] : "";
    $_SESSION['login'] = $login;

    $senha_input = isset( $_POST['senha_input']) ? $_POST['senha_input'] : "";
   

    if(isset( $_POST['submit'])){
        if($usuario==="aluno"){
            $senha_sql = "";
            //Resgatar aluno do banco de dados
            $sql_aluno = "SELECT senha FROM alunos WHERE matricula='$login'";
            $consulta_aluno = mysqli_query($conexao, $sql_aluno);
            if ($consulta_aluno) {
                while($row = $consulta_aluno->fetch_assoc()){
                    $senha_sql = $row['senha'];
                   }
                   $_SESSION['auth'] = true;
            if(password_verify($senha_input, $senha_sql ))
                header('Location: ./aluno/solicitacoes.php'); 
            else
                    echo "<div class='alert alert-warning' role='alert'> Senha ou matricula incorreta </div>";
        } else 
             echo "Error: " . $sql_aluno . ":-" . mysqli_error($conexao);
        }  
        else if($usuario==="adm"){
                $senha_sql = "";
                //Resgatar aluno do banco de dados
                $sql_coordenador = "SELECT senha FROM coordenadores WHERE email='$login'";
                $consulta_coordenador = mysqli_query($conexao, $sql_coordenador);
                if ($consulta_coordenador) {
                    while($row = $consulta_coordenador->fetch_assoc()){
                        $senha_sql = $row['senha'];
                       }
               
                if(password_verify($senha_input, $senha_sql ))
                    header('Location: ./coordenador/solicitations.php');  
                else
                        echo "<div class='alert alert-warning' role='alert'> Senha ou matricula incorreta </div>";
            } else 
                echo "Error: " . $sql_coordenador . ":-" . mysqli_error($conexao);
        }
    }

?>
<!DOCTYPE html>
<html>
    <meta charset="utf-8"/>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/index.css">
    <link rel="icon" href="./icon.png">
    <title>Solicitação de ACGs</title>
</head>
<body>
    <div style=" height: 100vh"> 
        <div > 
            <div class="header" id="header">
                <img src="unipampa png.png" class="img-fluid" alt="Logo Unipampa" id="logo">
            </div>
            <div class="card" id="card" style="margin-top: 70px"> 
                <div class="card-header" style="font-size: 18px">Login</div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-check" name="usuario" value="aluno">
                            <input class="form-check-input" type="radio" name="usuario" value="aluno">
                            <label class="form-check-label" >Aluno</label>
                        </div>
                        <div class="form-check" name="usuario"  value="adm">
                            <input class="form-check-input" type="radio" name="usuario"  value="adm">
                            <label class="form-check-label" >Coordernador/Professor</label>
                        </div>
                        <div class="mb-3" style="margin-top: 10px">
                            <div style="text-align: right; font-size: 12px; color: gray">Este login ainda não está vinculado com o sistema geral da Unipampa</div>
                            <label for="exampleFormControlInput1" class="form-label">Usuário</label>
                            <input type="text" name="login" class="form-control" id="exampleFormControlInput1" placeholder="Insira seu login/matricula" required autofocused>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Senha</label>
                            <input type="password" name="senha_input" class="form-control" id="exampleFormControlInput1" placeholder="*********" required autofocused>
                        </div>
                        <p style="font-size: 12px"> Não é cadastrado? <a href="register.php">Clique aqui</a></p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" name="submit" type="submit">Entrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <div style="bottom:5px; position:fixed; width:100%">
        <hr class="solid">
        <div class="container">
            <div class="row">
                <div class="col-6" >
                    <div style="font-size: 11px; width: 80%">
                        Desenvolvido pela coordenação de curso de Engenharia de Computação da Unipampa, Bruno Neves e Fabio Ramos, e a bolsista do curso Indra Araujo.
                    </div>
                </div>
                <div class="col" style="font-size: 11px; text-align: right">
                    Versão: 1
                </div>
            </div>
        </div>
    </div> 
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
</body>
</html>
