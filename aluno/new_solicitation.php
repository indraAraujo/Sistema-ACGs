<?php
    date_default_timezone_set('America/Sao_Paulo');
    include_once("../conexao.php");

    session_start();
 
    require '../vendor/autoload.php';

    use \App\SendEmail\Email;
    $obMail = new Email;

    $matricula = $_SESSION['login'];


$grupos = array("Grupo I.1 - Atividades de Ensino", 
                "Grupo I.2 - Atividades de Ensino em Língua Inglesa",
                "Grupo II - Atividades de Pesquisa",
                "Grupo III - Atividades de Extensão",
                "Grupo IV - Atividades Culturais e Artísticas, Sociais e de Gestão");

if($_SESSION['auth'] && $_SESSION['login']!==""){
if(isset( $_POST['submit'])){
//Valores dos inputs do formulário
    $subcategoria= isset($_POST['atividades_select']) ? $_POST['atividades_select'] : "";
    $grupo=isset($_POST['grupo_select']) ? $_POST['grupo_select'] : "";
    $cargaHoraria =isset( $_POST['cargaHoraria'])? $_POST['cargaHoraria'] : "";
    $atividade = isset($_POST['atividade'])? $_POST['atividade'] : ""; 
    $mudarAtividade = isset($_POST['mudarAtividade'])? $_POST['mudarAtividade'] : 0; 

    if($mudarAtividade==='on')
        $mudarAtividade=1;
    else    
        $mudarAtividade=0;


//Data e hora atual da inserção
    $date = date("d/m/Y H:i:s ");

//Dados do aluno
    $nome = "";
    $email = "";
    $telefone = "";
    $curso = "";
//Conseguir dados do aluno para a inserção da nova solicitação
     $sql_aluno = "SELECT * FROM alunos WHERE matricula='$matricula'";
     $consulta_aluno = mysqli_query($conexao, $sql_aluno);
     foreach($consulta_aluno as $c){
         $values = array_values($c);
         $nome =  $values[1];
         $telefone = $values[2];
         $email = $values[3];
         $curso = $values[5];
     }


//Query para calcular carga horária do grupo requisitado
$horas_aceitas=true; 
$hora = $horas_requisitadas;
$sql_horas_grupo = "SELECT * FROM `wp_solicitations` WHERE matricula='$matricula' AND grupo='$grupo'";
$consulta_horas_grupo = mysqli_query($conexao, $sql_horas_grupo);
$horas_grupo=0;
if ($consulta_horas_grupo) {
    foreach($consulta_horas_grupo as $c){
        $values = array_values($c);
        $hora1 = $values[15];
        $resultado = $values[12];
        
        if($hora1==-1 && ($resultado=='Deferido' || $resultado=='DEFERIDO' || $resultado=='deferido') ){
            $hora1=$values[9];
        }
        $horas_grupo = $horas_grupo + $hora1;
    }
    switch($grupo){
        case 'Grupo I.1 - Atividades de Ensino':
            if($horas_grupo>60){
                $horas_aceitas=false;
                    print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
            }
            else{
                if(($hora+$horas_grupo) > 60){
                    $horas_aceitas=false;
                        print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                }
                else{
                    $horas_aceitas=true;
                    
                }
                
            } 
            break;
        case 'Grupo I.2 - Atividades de Ensino em Língua Inglesa':
            if($horas_grupo>30){
                $horas_aceitas=false;
                    print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
            }
            else{
                if(($hora+$horas_grupo) > 60){
                    $horas_aceitas=false;
                        print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                }
                else{
                    $horas_aceitas=true;
                }
                
            } 
            break;
        case 'Grupo II - Atividades de Pesquisa':
            if($horas_grupo>60){
                $horas_aceitas=false;
                    print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
            }
            else{
                if(($hora+$horas_grupo) > 60){
                    $horas_aceitas=false;
                        print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                }
                else{
                    $horas_aceitas=true;
                }
                
            } 
            break;
        case 'Grupo III - Atividades de Extensão' :
            if($horas_grupo>60){
                $horas_aceitas=false;
                    print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
            }
            else{
                if(($hora+$horas_grupo) > 60){
                    $horas_aceitas=false;
                        print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                }
                else{
                    $horas_aceitas=true;
                }
                
            } 
            break;
        case 'Grupo IV:  Atividades Culturais e Artísticas, Sociais e de Gestão.':
            if($horas_grupo>60){
                $horas_aceitas=false;
                    print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
            }
            else{
                if(($hora+$horas_grupo) > 60){
                    $horas_aceitas=false;
                        print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                }
                else{
                    $horas_aceitas=true;
                }
                
            } 
        break;
    }  


} else {
    echo "<div class='alert alert-warning' role='alert'>Houve um erro no servidor ao recuperar seus dados. Tente novamente</div>";
}
   
//Carregamento do documento
   if(isset($_FILES['documento'])){
       $documento = $_FILES['documento'];
        
       if($documento['error'])
         print "<div class='alert alert-warning' role='alert'> Houve uma falha ao carregar ser documento </div> ".$documento['error'];


       $pasta = "documentos/";
       $novo_nome_doc = uniqid();
       $extensao = strtolower(pathinfo($_FILES['documento']["name"], PATHINFO_EXTENSION));

       $basename = $novo_nome_doc.'.'.$extensao;

       $destination_path = getcwd().DIRECTORY_SEPARATOR;
       $target_path = $destination_path . 'documentos/'. basename( $basename);
       $arquivo_aceito= move_uploaded_file($_FILES['documento']['tmp_name'], $target_path);

       if($arquivo_aceito){
        $save_path = $pasta.$basename;
        //Inserir nova solicitação
            if($arquivo_aceito===true && $horas_aceitas===true){   
                if($atividade!==""){                 
                    $sql_solicitacao = "INSERT INTO wp_solicitations (dateTime, email, semestre,  curso, nome_aluno, matricula, telefone_aluno, atividade, carga_horaria_requisitada, grupo,subcategoria, documentos, resultado, motivo, carga_horaria_aproveitada, mudarAtividade)
                    VALUES ('$date','$email', '2022/1',  '$curso', '$nome', '$matricula','$telefone', '$atividade', '$cargaHoraria', '$grupo', '$subcategoria', '$save_path', 'PENDENTE', 'PENDENTE', '-1', '$mudarAtividade' )";
                    $new_solicitacao =  mysqli_query($conexao, $sql_solicitacao);
                    $atividade="";
                    $cargaHoraria="";
                    $grupo="";
                    $subcategoria="";
                    $save_path="";
                    $mudarAtividade= 0;
                    $subject="Sistema de Solicitação de ACG - Unipampa";
                    $message = "Prezado(a), você fez uma solicitação de ACG no Sistema de Solicitação de ACG  da Unipampa. </br></br>Não é você? Entre em contato com o seu coordenador de curso.";
                    $obMail->sendEmail($email,$subject,$message);
                    if($new_solicitacao){
                        print "<div class='alert alert-success' role='alert'>Nova solicitação realizada com sucesso</div>";
                 } else
                        print "<div class='alert alert-warning' role='alert'>Algo não saiu como esperado, tente novamente.</div>";
                }else{
                    print "<div class='alert alert-warning' role='alert'>Não foi possível enviar a solicitação. Verifique se todas as informações estão corretas.</div>";
                }
            } 
        }
       }    
       else 
            print "<div class='alert alert-warning' role='alert'>Algo não saiu como esperado, tente novamente.</div>";

}
}else{
    print "<div class='alert alert-warning' role='alert'>É necessário fazer o login</div>";
}
  mysqli_close($conexao);
  
   
?>
<!DOCTYPE html>
<html>
    <meta charset="utf-8"/>
<head>       
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.js"></script>



    <title>Nova Solicitação</title>
</head>
<body>
    <?php
        include('./header.php')
    ?>

<div class="card" id="card" style="width: 60%; margin-top: 20px">
    <div class="card-header" style="font-size: 18px">Nova Solicitação</div>
    <div class="card-body">
    <form method="POST" enctype="multipart/form-data" action="">
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Descrição da Atividade</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" name="atividade" rows="1" placeholder="Descrição resumida da atividade complementar"></textarea>
                <div style="text-align: right; font-size: 12px; color: gray">O texto não pode conter aspas simples (')</div>
            </div>
            <hr>
            <div style="font-size: 16px; margin-bottom: 10px">Selecione o grupo e a sua atividade que mais se encaixe</div>
           
            <div class="container" style="margin-top: 20px">
                <div class="row">
                    <div class="col-6">
                        <label for="grupo_select" class="form-label" style="font-size: 14px">Grupo</label>
                        <select class="form-select" name="grupo_select" id="grupo_select" >
                            <option >Selecione a atividade</option>
                            <?php
                                forEach($grupos as $option){
                                    print "<option value='$option'>$option</option>";
                                }
                            ?>
                        </select>
                    </div>  
                    <div class="col-6">
                        <label for="atividades_select" class="form-label" style="font-size: 14px">Atividade do Grupo</label>
                        <select class="form-select" name="atividades_select" id="atividades_select">
                            <option value="">Selecione a atividade</option>
                        </select>
                    </div> 
            </div>
            </div>
           
            <hr>
            <label for="exampleFormControlInput1" class="form-label">Carga Horaria</label>
            <input type="decimal" class="form-control" name="cargaHoraria" id="exampleFormControlInput1" placeholder="00" style="width: 20%">      
            <hr>

            <div class="form-check form-switch" style="margin-top:20px">
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="mudarAtividade">
                <label class="form-check-label" for="flexCheckDefault" style="font-size: 11px; margin-top: 5px">
                   Eu aceito que o coordenador mude a atividade caso a minha carga horária exceda na qual eu escolhi.
                </label>
            </div>
            <div class="mb-3"  style="margin-top: 20px">
            <label for="documento" class="form-label">Escolha o documento comprovador</label>
            <br>
            <input name="documento" id="documento" type="file"/>
            </div>
         <div class="d-grid gap-2">
         <button class="btn btn-success" name="submit" type="submit" >Enviar Solicitação</button>
        </div>
        </form>
        
    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
</body>
</html>
  <script>
      $(document).ready(function() {
        atividades_I1= ["Disciplina de graduação",
                            "Cursos de extensão",
                            "Monitoria",
                            "Participação em projetos de ensino",
                            "Cursos de aperfeiçoamento",
                            "Participação em eventos de ensino, pesquisa ou extensão",
                            "Disciplina de graduação com o conteúdo ligado ao aprendizado de língua estrangeira",
                            "Curso de idiomas estrangeiros",
                            "Certificados de proficiência em lingua estrangeira oficiais"];
         atividades_I2 = ["Disciplina de graduação com o conteúdo ligado ao aprendizado de língua inglesa",
                            "Cursos de inglês",
                            "Certificados de proficiência em língua inglesa" ];
         atividades_II= ["Participação em projetos de pesquisa",
                            "Publicação em eventos de iniciação científica",
                            "Publicação em eventos nacionais (primeiro autor)",
                            "Publicação em eventos nacionais",
                            "Publicação em eventos internacionais (primeiro autor)",
                            "Publicação em eventos internacionais",
                            "Publicação em periódico nacional (primeiro autor)",
                            "Publicação em periódico nacional ",
                            "Publicação em periódico internacional (primeiro autor)",
                            "Publicação em periódico internacional",
                            "Publicação de capítulo de livro (primeiro autor)",
                            "Publicação de capítulo de livro",
                            "Publicação de livro",
                            "Apresentação de trabalhos em eventos de iniciação científica",
                            "Apresentação de trabalhos em eventos nacionais",
                            "Apresentação de trabalhos em eventos internacionais"];
         atividades_III= ["Participação em projetos de extensão",
                            "Estágios não obrigatórios na área",
                        "Ministrante de curso de extensão",
                            "Monitor de curso de extensão ",
                            "Organização de eventos acadêmicos que promovam divulgação do conhecimento ",
                            "Participação em eventos que promovam a divulgação da UNIPAMPA para a comunidade",
                            "Representação em órgãos colegiados da comunidade",
                          "Outras atividades relativas à extensão"];
         var atividades_IV= ["Representação em órgãos colegiados ",
                            "Participação em comissões da UNIPAMPA",
                            "Participação em diretórios acadêmicos",
                            "Participação, como bolsista, em atividades de iniciação ao trabalho técnico-profissional e de gestão acadêmica",
                            "Organização de eventos ou atividades culturais ou artísticas",
                            "Organização de ações beneficentes ou de cunho social",
                            "Trabalho voluntário de cunho social ou ambiental",
                            "Expectador de sessões de cinema, teatro ou espetáculos musicais",
                            "Participação em sessões de cinema, teatro ou saraus que  envolvam discussão de obras ou autores",
                            "Visita a museus ou exposições",
                            "Outras atividades"];
            
            $('#grupo_select').change(function(){
                    switch($(this).val()){
                    case 'Grupo I.1 - Atividades de Ensino':
                        for (var i=0;i<atividades_I1.length;i++){
                            $('<option/>').val(atividades_I1[i]).html(atividades_I1[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo I.2 - Atividades de Ensino em Língua Inglesa':
                        for (var i=0;i<atividades_I2.length;i++){
                            $('<option/>').val(atividades_I2[i]).html(atividades_I2[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo II - Atividades de Pesquisa':
                        for (var i=0;i<atividades_II.length;i++){
                            $('<option/>').val(atividades_II[i]).html(atividades_II[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo III - Atividades de Extensão':
                        for (var i=0;i<atividades_III.length;i++){
                            $('<option/>').val(atividades_III[i]).html(atividades_III[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo IV - Atividades Culturais e Artísticas, Sociais e de Gestão':
                        for (var i=0;i<atividades_IV.length;i++){
                            $('<option/>').val(atividades_IV[i]).html(atividades_IV[i]).appendTo('#atividades_select');
                        }
                        break;
                }
            })
        })
    </script>
