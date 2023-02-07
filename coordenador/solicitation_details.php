<?php
    include_once("../conexao.php");
    session_start();

 /*
    inicialização das variáveis da análise
 */
    $motivo = "";
    $horas_aceitas=true; 

/*
    inicialização das variáveis do aluno e da solicitação
*/
    $nome = "";
    $matricula="";
    $semestre = "";
    $atividade="";
    $grupo="";
    $subcategoria="";
    $horas_requisitadas="";
    $documento="";
    $mudarAtividade = 0;

/*
    população do dropdown de grupo
*/

$grupos = array("Grupo I.1 - Atividades de Ensino", 
                "Grupo I.2 - Atividades de Ensino em Língua Inglesa",
                "Grupo II - Atividades de Pesquisa",
                "Grupo III - Atividades de Extensão",
                "Grupo IV - Atividades Culturais e Artísticas, Sociais e de Gestão");
/*
    código da solicitação selecionada na Tela de Solicitações Pendentes
*/
    $id = $_SESSION['id'];


/*
    requisição ao MySQL para pegar todas as informações da solicitação
    de acordo com o código da solicitação
*/
    $sql_get = "SELECT * FROM wp_solicitations WHERE solicitation_id='$id'";
    $consulta_get = mysqli_query($conexao, $sql_get);
    
    
/*
    alimentação das variáveis dos alunos com as informações recebidas a partir
    da requisição feita anteriormente
*/
    foreach($consulta_get as $c){
        $values = array_values($c);
        $nome =  $values[5];
        $matricula = $values[6];
        $semestre = $values[3];
        $atividade = $values[8];
        $grupo = $values[10];
        $subcategoria = $values[11];
        $horas_requisitadas=$values[9];
        $documento = $values[12];
        $mudarAtividade = $values[16];
    }

/*
    busca de atividades no mesmo grupo para coletar a carga horária total
    já solicitada neste grupo pelo aluno
*/
    $sql_getHoursByGroup = "SELECT carga_horaria_aproveitada FROM wp_solicitations WHERE matricula='$matricula' AND grupo='$grupo' AND resultado='Deferido'";
    $consulta_getHoursByGroup = mysqli_query($conexao, $sql_getHoursByGroup);

    $totalHoursByGroup=0;
    while($row = mysqli_fetch_array($consulta_getHoursByGroup)){
       $totalHoursByGroup+= $row[0]; 
    }

/*
    busca de atividades na mesma subcategoria para coletar a carga horária 
    total já solicitada nesta subcategoria pelo aluno
*/
    $sql_getHoursBySubgroup = "SELECT carga_horaria_aproveitada FROM wp_solicitations WHERE matricula='$matricula' AND subcategoria='$subcategoria' AND resultado='Deferido'";
    $consulta_getHoursBySubgroup = mysqli_query($conexao, $sql_getHoursBySubgroup);

    $totalHoursBySubgroup=0;
    while($row = mysqli_fetch_array($consulta_getHoursBySubgroup)){
    $totalHoursBySubgroup+= $row[0]; 
    }

/*
    ação após o usuário ter clicado no botão de Enviar
*/
    if(isset( $_POST['submit'])){

        //recebe o dado do radio box de deferimento
        $resultado = isset($_POST['deferimento']) ? $_POST['deferimento'] : 'PENDENTE';
        
        /*
            - comparação do dado do radio box
            - se for 0 -> 'Indeferido'
            - se for 1 -> 'Deferido'
            - variável $resultado recebe a string correspondente a seleção
              para salvar no banco de dados
        */

        if($resultado==='0'){
            $resultado = "Indeferido";
            //recebe o dado do campo de texto de Motivo
            $motivo=isset($_POST['motivo']) ? $_POST['motivo'] : '-';
        }else{
            $resultado = "Deferido";
        }

        
        // recebe a seleção do switch de escolher entre a carga horária requisitada ou definir outra
        $hora = isset($_POST['hora_requisitada']) ? $_POST['hora_requisitada'] : 'PENDENTE';

        /*
            - ação de acordo com a escolha do switch 
            - se 'on'  -> usar carga horária requisitada
            - se 'off' -> usar nova definição de carga horária requisitada
            - variável $hora recebe a carga horária correspondente a seleção
              para salvar no banco de dados (caso a solicitação for deferida)
        */
        if($resultado!=='Indeferido'){
             if($hora==='on')
                $hora=$horas_requisitadas;
            else
                $hora=isset($_POST['nova_hora']) ? $_POST['nova_hora'] : 'PENDENTE';
        }else
            $hora = 0;
       

         // recebe a seleção do switch de definir outra atividade 
         $novaAtividade = isset($_POST['mudar_atividade']) ? $_POST['mudar_atividade'] : '';

         /*
             - ação de acordo com a escolha do switch
             - se 'on'  -> mudar atividade
             - se 'off' -> usar atividade solicitada
             - variaveis $grupo e $subcategoria recebem os valores novos 
               de acordo com a seleção para salvar no banco de dados
         */
         if($novaAtividade==='on'){
            $grupo = isset($_POST['grupo_select']) ? $_POST['grupo_select'] : 'PENDENTE';
            $subcategoria=isset($_POST['atividade_select']) ? $_POST['atividade_select'] : 'PENDENTE';
         }

        /*
            - envia as informações da análise para o banco de dados apenas se a 
              variável $resultado estiver preenchida e não vazia
        */
        if($resultado !==""){
            // envio da análise ao banco de dados
            $sql_update = "UPDATE wp_solicitations SET `grupo`='$grupo', `subcategoria`='$subcategoria', `resultado`='$resultado',`motivo`='$motivo',`carga_horaria_aproveitada`='$hora' WHERE solicitation_id='$id'";
            $consulta_update = mysqli_query($conexao, $sql_update);

            if($consulta_update)
                echo "<div class='alert alert-success' role='alert'>Análise da solicitação enviada com sucesso.</div>";
            else
                echo "<div class='alert alert-warning' role='alert'>Não foi possível enviar a análise da solicitação. Tente novamente</div>";
        
        }
    }



?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <meta charset="utf-8"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/solicitation_details.css">
    <link rel="icon" href="../icon.png">
    <title>Detalhe da Solicitação</title>
</head>
<body>
    <?php
        include('./header.php');
    ?>

    <div class="container" id="container" style="background: #FFFFFF;
                                                box-shadow: 4px -4px 8px 6px rgba(0, 0, 0, 0.25);
                                                margin-bottom: 20px;
                                                margin-top: 40px">
        <?php
                print "<div style='display: flex;
                                   flex-direction: 'row';'>";
                        print "<div style='margin-top: 10px;
                                        font-size: 16px;'> $nome </div>";
                        print "<div style='font-size: 16px;
                                        margin-top: 10px;
                                        margin-left: 40px;'>Codigo # </div>";
                        print "<div style='font-size: 16px;
                                        margin-top: 10px;'> $id </div>";
                        print "<div style='font-size: 16px;
                                        margin-top: 10px;
                                        margin-left: 190px'>$semestre </div>";
                print "</div>";
                print "<div style='margin-top: 10px;
                                    font-size: 14px;'> $matricula</div>";

                print    "<div style='border: 1px solid #C6C6C6; 
                                    margin-top: 10px'> </div>";

                print "<div style='display: flex;
                                  flex-direction: 'row';'>";
                    print "<div style='width: 60%'>";
                        print "<div style='background: #FFFFFF;
                                       box-shadow: 4px -4px 11px 1px rgba(0, 0, 0, 0.16);
                                        margin-top: 30px;
                                        width: 100%;
                                        padding-bottom: 20px'> ";
                                print "<div style='font-size: 16px;
                                                   color: #7E7E7E;
                                                   margin-left: 10px'> Atividade </div>";
                                print "<div style='margin-top: 20px;
                                                   margin-left: 10px;
                                                   font-size: 12'>$atividade </div>";
                                print "</div>";
                        print "<div style='background: #FFFFFF;
                                            box-shadow: 4px -4px 11px 1px rgba(0, 0, 0, 0.16);
                                            margin-top: 30px;
                                            width: 100%'> ";
                                print "<div style='font-size: 16px;
                                                    color: #7E7E7E;
                                                    margin-left: 10px'> Subgrupo Requisitado </div>";
                                print "<div style='margin-top: 20px;
                                                    margin-left: 10px;
                                                    font-size: 12'>$subcategoria </div>";
                        print "</div>";
                   print "</div>";
                        
                        print "<div style='margin-left: 30px;
                                           width: 40%'>";
                                print "<div style='background: #FFFFFF;
                                       box-shadow: 4px -4px 11px 1px rgba(0, 0, 0, 0.16);
                                        margin-top: 30px;
                                        width: 100%'> ";
                                        print "<div style='font-size: 16px;
                                                            color: #7E7E7E;
                                                            margin-left: 10px'> Grupo Requisitado </div>";
                                        print "<div style='margin-top: 20px;
                                                            margin-left: 10px;
                                                            font-size: 12'>$grupo </div>";
                                print "</div>";
                                print "<div style='background: #FFFFFF;
                                                   box-shadow: 4px -4px 11px 1px rgba(0, 0, 0, 0.16);
                                                    margin-top: 30px;
                                                    width: 100%'> ";
                                print "<div style='font-size: 16px;
                                                    color: #7E7E7E;
                                                    margin-left: 10px'> Carga Horaria Requisitada </div>";
                                print "<div style='margin-top: 20px;
                                                    margin-left: 10px;
                                                    font-size: 12'> $horas_requisitadas</div>";
                        print "</div>";
                        print "</div>";
                print "</div>";
                print "<div class='container' style='background: #FFFFFF;
                                                    box-shadow: 4px -4px 11px 1px rgba(0, 0, 0, 0.16);
                                                    margin-top: 30px;
                                                    width: 50%'>";
                print "<div class='row'>";
                print " <div class='col'>" ;
                    print "<div style='margin-top: 20px'>";
                        print "<a href='../aluno/$documento' target='_blank'>";
                            print "<svg xmlns='http://www.w3.org/2000/svg' width='30' height='30' fill='currentColor' class='bi bi-file-earmark-arrow-down' viewBox='0 0 16 16'>
                                        <path d='M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293V6.5z'/>
                                        <path d='M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z'/>
                                    </svg>";
                        print "</a>";
                    print  "</div>";
                print  "</div>";
                print  "<div class='col'>  Clique no ícone para abrir o documento </div>";
                print "</div>";
                print"</div>";
        ?>
        <form method="POST" action="" >
            <div class="container">
                <div style="margin-top: 20px;
                            margin-left: 110px;
                            display: flex;
                            flex-direction: row">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="deferimento"  value="1" id="deferimento">
                        <label class="form-check-label" for="flexRadioDefault1">Deferido</label>
                    </div>
                    <label style="margin-left: 90px; display: none " id="motivo_indeferimento">Motivo do Indeferimento </div>
                </div>
                <div style="margin-left: 122px;
                            display: flex;
                            flex-direction: row">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="deferimento" value="0" id="indeferimento">
                        <label class="form-check-label" for="flexRadioDefault1">Indeferido</label>
                    </div>
                    <div style=" margin-left: 60px; display: none" id="motivo_indeferimento_input">
                        <input class="form-control" list="motivo" name="motivo" id="motivo" placeholder="Selecione o Motivo...">
                        <datalist id="motivo">
                            <option value="Grupo Errado">
                            <option value="Documento incompleto">
                            <option value="Atividade fora dos anos de graduação">
                        </datalist>
                    </div>
                </div>
                <div style="width: full; display: none; justify-content: center; margin-top:10px; margin-bottom:-30px" id="alert_outOfRangeHours">
                    <div style="width: 50%"> 
                        <div class='alert alert-danger' role='alert' style="width: 100%">Carga horária excede o limite</div>
                    </div>
                     <div class="d-grid gap-2">
                        <button type="button" id="last_solicitations" class="btn btn-secondary"  style="margin-left: 10px" >Solicitações Prévias</button>
                    </div>
                </div>
                
                <div style="margin-top: 20px;
                            margin-left: 110px;
                            display: flex;
                                flex-direction: row">
                        <div>
                            <label>Carga Horária Requisitada</label>
                            <div class="form-check form-switch" style="margin-left: 90px;
                                                                        margin-top: 15px"
                                                                        >
                                <input class="form-check-input" type="checkbox" role="switch" id="hora_requisitada" name="hora_requisitada">
                            </div>
                        </div>
                        <div style="margin-left: 30px; display: block" id="nova_ch">
                            <label for="exampleFormControlInput1" class="form-label">Carga Horaria Ajustada</label>
                            <input type="number" 
                                    class="form-control" 
                                    id="newHour" 
                                    placeholder="00"
                                    style="width: 30%; margin-left:40px"
                                    name="nova_hora" >
                        </div>
                </div>
            <div style="display: none" id="showMudarAtividade">  
                <div style="display: flex; flex-direction: row; margin-left: 10px">
                    <label>Mudar Atividade</label>
                    <div class="form-check form-switch" style="margin-left: 10px">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="mudar_atividade">
                    </div>
                </div>
                <div class="container" style="margin-top: 5px">
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
            </div>
            <div class="d-grid gap-2">
                  <button type="submit"  name="submit" class="btn btn-success"  style="margin-bottom: 30px" >Enviar</button>
            </div>
        </form>    
    </div>
    
</body>
</html>
<script>
      $(document).ready(function() {

        /*
            listagem das atividades possíveis dentre cada grupo
        */
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
            
            /*
                população do dropdown de atividades de acordo com o grupo selecionado
            */
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

            /*
                visualização da seção de mudar de grupo/atividade de acordo
                com a permissão do aluno armazenado no banco de dados ($mudarAtividade)
            */
            const mudarAtividade = "<?php echo $mudarAtividade ?>";
            if(mudarAtividade==="1"){
                document.getElementById("showMudarAtividade").style.display = "inherit"
            }

            /*
                visualização do input para o motivo do indeferimento de acordo
                com a seleção dos radio button de Deferiment
            */
            $('#indeferimento').change(function(){
                document.getElementById("motivo_indeferimento").style.display = "inherit"
                document.getElementById("motivo_indeferimento_input").style.display = "inherit"
            })
            $('#deferimento').change(function(){
                document.getElementById("motivo_indeferimento").style.display = "none"
                document.getElementById("motivo_indeferimento_input").style.display = "none"
            })

            /* 
                visualização do input para nova carga horária de acordo
                com a seleção do switch de Carga Horária Requisitada
            */
           $('#hora_requisitada').change(function(){
                var nova_ch_component = document.getElementById("nova_ch")
                if( nova_ch_component.style.display==='block')
                 nova_ch_component.style.display = "none"
                else    
                 nova_ch_component.style.display = "block"

           })

           //Variável com o grupo da atividade solicitada
           const grupo = "<?php echo $grupo ?>";
           //Variável com a subcategoria da atividade solicitada
           const subcategoria = "<?php echo $subcategoria ?>";
           //Variável com a quantidade de horas requisitadas
           const horas_requisitadas = "<?php echo $horas_requisitadas ?>";

           //Variável com carga horária das solicitações de atividade já deferidas do mesmo grupo
           const totalHoursByGroup = "<?php echo $totalHoursByGroup ?>";
           //Variável com carga horária das solicitações de atividade já deferidas da mesma subcategoria
           const totalHoursBySubgroup = "<?php echo $totalHoursBySubgroup ?>";
           
           
           const checkHoursBySubgroup = (hours) =>{
                let hoursOutOfRange = false;
                switch(subcategoria){
                    case 'Disciplina de graduação':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Cursos de extensão':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){ //o que colocar aqui -> 25% CH, Max 20/curso
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Monitoria':
                        if(hours > 60){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){ //o que colocar aqui -> CH semanal/semestre
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Participação em projetos de ensino':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){//o que colocar aqui -> CH semanal/semestre
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Cursos de aperfeiçoamento':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Participação em eventos de ensino, pesquisa ou extensão':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 4){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Disciplina de graduação comconteúdo ligado ao aprendizadode língua estrangeira (exceto inglês)':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Cursos de idiomas estrangeiros (exceto inglês)':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Certificados de proficiência em língua estrangeira oficiais (exceto inglês)':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Disciplina de graduação com conteúdo ligado ao aprendizado de língua inglesa':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Cursos de inglês':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Certificadosde proficiência em língua inglesa':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Participação em projetos de pesquisa':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){ //o que colocar aqui -> CH semanal/semestre
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Publicação em eventos de iniciação científica':
                        if(hours > 6){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 2){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Publicação em eventos nacionais (primeiro autor)':
                        if(hours > 12){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 4){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Publicação em eventos nacionais':
                        if(hours > 6){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 2){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Publicação em eventos internacionais (primeiro autor)':
                        if(hours > 8){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 24){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Publicação em eventos internacionais':
                        if(hours > 12){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 4){
                            hoursOutOfRange = true;
                        }
                        break;   
                    case 'Publicação em periódico nacional (primeiro autor)':
                        if(hours > 48){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 16){
                            hoursOutOfRange = true;
                        }
                        break;  
                    case 'Publicação em periódico nacional':
                        if(hours > 24){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 8){
                            hoursOutOfRange = true;
                        }
                        break;  
                    case 'Publicação em periódico internacional (primeiro autor)':
                        if(hours > 60){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 20){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Publicação em periódico internacional':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Publicação de capítulo de livro (primeiro autor)':
                        if(hours > 48){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 16){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Publicação de capítulo de livro':
                        if(hours > 25){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 8){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Publicação de livro':
                        if(hours > 60){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 20){
                            hoursOutOfRange = true;
                        }
                        break;  
                    case 'Apresentação de trabalhos em eventos de iniciação científica':
                        if(hours > 3){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 1){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Apresentação de trabalhos em eventos nacionais':
                        if(hours > 6){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 2){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Apresentação de trabalhos em eventos internacionais':
                        if(hours > 12){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 4){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Participação em projetos de extensão':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 6){ //o que coloco aqui -> CH semanal/semestre
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Estágios não obrigatórios na área de Engenharia de Computação':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 6){ //o que coloco aqui -> 15h/semestre
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Ministrante de curso de extensão':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 4){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Monitor de curso de extensão':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 2){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Organização de eventos acadêmicos que promovam divulgação do conhecimento':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 4){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Participação em eventos que promovam a divulgação da UNIPAMPA para a comunidade':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 4){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Representação em órgãos colegiados da comunidade':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 1){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Outras atividades relativas à extensão':
                        if(hours > 10){
                            hoursOutOfRange = true;
                        }
                        break; 
                    case 'Representação em órgãos colegiados':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 1){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Participações em comissões da UNIPAMPA':
                        if(hours > 20){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 1){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Participação em diretórios acadêmicos':
                        if(hours > 10){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 1){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Participação, como bolsista, em atividades de iniciação ao trabalho técnico-profissional e de gestão acadêmica':
                        if(hours > 30){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 15){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Organização de eventos ou atividades culturais ou artísticas':
                        if(hours > 15){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 5){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Organização de ações beneficientes ou de cunho social':
                        if(hours > 40){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 5){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Trabalho voluntário de cunho social ou ambiental':
                        if(hours > 40){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 10){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Expectador de sessões de cinema, teatro ou espetáculos musicais':
                        if(hours > 5){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 0.5){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Participação em sessões de cinema, teatro ou saraus que envolvam discussão de obras ou autores':
                        if(hours > 10){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 1){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Visita a museus ou exposições':
                        if(hours > 5){
                            hoursOutOfRange = true;
                        }else if(horas_requisitadas > 1){
                            hoursOutOfRange = true;
                        }
                        break;
                    case 'Outras atividades':
                        if(hours > 5){
                            hoursOutOfRange = true;
                        }
                        break;  
                }

                return hoursOutOfRange;
           }

           /*
              Calculo se a carga horária excede o limite
           */
           const checkHours = (hours) => {
                let hoursOutOfRange = false;
                
                switch(grupo){
                    case 'Grupo I.2 - Atividades de Ensino em Língua Inglesa':
                        if((hours + parseInt(totalHoursByGroup, 10))>30){
                            hoursOutOfRange = true;
                        }else{
                            hoursOutOfRange = checkHoursBySubgroup(hours + parseInt(totalHoursBySubgroup, 10));
                        }
                        break;
                    default:
                        if((hours + parseInt(totalHoursByGroup, 10))>60){
                            hoursOutOfRange = true;
                        }else{
                            hoursOutOfRange = checkHoursBySubgroup(hours + parseInt(totalHoursBySubgroup, 10));
                        }
                        break;
                }
                if(hoursOutOfRange){
                    setAlertOutOfRangeHours();
                }else{
                    hideAlertOutOfRangeHours();
                }
           }

           const setAlertOutOfRangeHours = () => {
             document.getElementById("alert_outOfRangeHours").style.display = "flex";
           }

           const hideAlertOutOfRangeHours = () => {
            document.getElementById("alert_outOfRangeHours").style.display = "none";
           }
           
           checkHours(parseInt(horas_requisitadas,10));

           $( "#newHour" ).keyup(function() {
                checkHours(  parseInt(document.getElementById("newHour").value, 10) );
            });

            $( "#newHour" ).change(function() {
                checkHours( parseInt(document.getElementById("newHour").value, 10));
            });

            function getLastSolicitations() {
                window.open('https://www.geeksforgeeks.org/how-to-open-url-in-new-tab-using-javascript/', '_blank');
            }
            let last_solicitations = document.getElementById("last_solicitations");
            last_solicitations.addEventListener('click', event => {
                getLastSolicitations();
            });
         
        })
    </script>
