
<?php
    include_once("conexao.php");
    session_start();
   
    require __DIR__.'/vendor/autoload.php';
    use \App\SendEmail\Email;
    $obMail = new Email;



    $nome_aluno =  isset($_POST['nome_aluno'])?$_POST['nome_aluno']:"";
    $matricula =  isset($_POST['matricula'])?$_POST['matricula']:"";
    $curso_aluno =  isset($_POST['curso_aluno'])?$_POST['curso_aluno']:"";
    $email_aluno =  isset($_POST['email_aluno'])?$_POST['email_aluno']:"";
    $telefone =  isset($_POST['telefone'])?$_POST['telefone']:"";
    $senha_aluno =  isset($_POST['senha_aluno'])?$_POST['senha_aluno']:"";
    $hashed_senha_aluno = password_hash($senha_aluno, PASSWORD_DEFAULT);

    $subject="Sistema de Solicitação de ACG - Unipampa";
    $body = "Prezado(a), você se cadastrou no Sistema de Solicitação de ACG  da Unipampa. </br> </br>Não é você? Entre em contato com o seu coordenador de curso";
    if($nome_aluno!==''){
      if(strpos($email_aluno, '.aluno@unipampa.edu.br')){
          $sql = "INSERT INTO alunos (nome, telefone, email, matricula, curso, senha)
          VALUES ('$nome_aluno','$telefone','$email_aluno', '$matricula', '$curso_aluno', '$hashed_senha_aluno')";
          $_SESSION['login'] = $matricula;
          
          if (mysqli_query($conexao, $sql)) {
              $obMail->sendEmail($email_aluno, $subject, $body);
              $_SESSION['auth']=true;
              header('Location: ./aluno/solicitacoes.php'); 
          } else {
            print "<div class='alert alert-warning' role='alert'> Ocorreu algum erro. Tente novamente.</div>";
          }
            mysqli_close($conexao);
      }else{
	$_SESSION['auth']=false;
        print "<div class='alert alert-warning' role='alert'>O email inserido não é institucional</div>";
      }
    }
    

?>
<!DOCTYPE html>
<html>
    <meta charset="utf-8"/>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.js"></script>
    <link rel="stylesheet" href="./css/register.css">
    <link rel="icon" href="icon.png">
    <title>Cadastro</title>
</head>
<body>
    <div class="header" id="header">
        <img src="unipampa png.png" class="img-fluid" alt="Logo Unipampa" id="logo">
    </div>
    <div class="card" id="card" style="margin-top:10px; margin-bottom: 20px">
    <div class="card-header">Cadastro</div>
    <div class="card-body">
   
      <form method="POST" action="">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Nome</label>
            <input type="text" name="nome_aluno" class="form-control" id="exampleFormControlInput1" placeholder="Nome Sobrenome" required autofocused>
         </div>
         <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Matricula</label>
            <input type="text" name="matricula" class="form-control" id="exampleFormControlInput1" placeholder="000000000" required autofocused>
         </div>
         <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Curso</label>
            <input type="text" name="curso_aluno" class="form-control" id="exampleFormControlInput1" placeholder="Engenharia de Computação" required autofocused>
         </div>
         <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">E-mail</label>
            <input type="email" name="email_aluno" class="form-control" id="exampleFormControlInput1" placeholder="nomesobrenome.usuario@unipampa.edu.br" required autofocused>
         </div>
         <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Telefone/Celular para Contato</label>
            <input type="text" name="telefone" class="form-control" id="exampleFormControlInput1" placeholder="(00) 0 0000-0000" required autofocused>
         </div>
         <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Senha</label>
            <input type="password" name="senha_aluno" class="form-control" id="exampleFormControlInput1" placeholder="*********" required autofocused>
         </div>
         
         <div class="d-grid gap-2">
         <button class="btn btn-success" type="submit">Cadastrar</button>
        </div>
        </form>
 

</div>
      
        
    </div>
  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
</body>
</html>
