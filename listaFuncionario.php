<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';
$id_funcionario = 0;
$id_cidade = 0;
$mensagem = null;

$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR;
try {
    $campoConsultar = isset($_POST['btConsultar']) ? $_POST['campoConsultar'] : '';

    if (isset($_REQUEST['btnExcluir'])) {

        try {
            $transac = 0;
            $id_funcionario = $_GET['id_funcionario'];
            $id_cidade = $_GET['id_cidade'];

            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível iniciar a transação");}
            $transac = 1;

            $sql = sprintf("DELETE FROM funcionario WHERE id_funcionario = %s ", $id_funcionario);
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possivel excluir este funcionário");}
            
            $sql = sprintf("DELETE FROM endereco WHERE id_cidade = %s",$id_cidade);
            $result = pg_query(ConnectPG(),$sql);
            if(!$result){throw new Exception("Não foi possível excluir este endereço");}

            $result = pg_query(ConnectPG(), 'commit');
            if (!$result) {throw new Exception("Não foi possivel concluir a transação");}
            
            echo "<SCRIPT type='text/javascript'> 
                        alert('Funcionário Excluído!');
                        window.location.replace(\"listaFuncionario.php\");
                    </SCRIPT>";
        } catch (Exception $e) {
            if ($transac) {
                pg_query(ConnectPG(), 'rollback');
            }
            $mensagem = sprintf("<div class='alert alert-danger' role='alert'>
                        <strong>%s</strong></div>", $ex->getMessage());
        }
    }

    $consultar = '';
    if (isset($_POST['btConsultar'])) {
        $acao = ACAO_CONSULTAR;
        $param = trim($_POST['campoConsultar']);
        $consultar = sprintf(" AND a.nome LIKE %s OR b.descricao LIKE %s ", PrepararLike($param), PrepararLike($param));
    }

    if (isset($_POST['btConsultar'])) {
        $sql = "SELECT  a.id_funcionario,a.id_cidade,a.nome, a.dt_nascimento AS \"data de nascimento\", a.email, a.dt_admissao AS admissao, b.descricao AS funcao, c.nome_cidade AS cidade
                FROM funcionario a
                INNER JOIN funcao b ON a.id_funcao = b.id_funcao
                INNER JOIN endereco c ON a.id_cidade = c.id_cidade
                WHERE id_funcionario = $id_funcionario";
    }

    $sql = "SELECT a.id_funcionario,a.id_cidade,a.nome, a.dt_nascimento AS \"data de nascimento\", a.email, a.dt_admissao AS admissao, b.descricao AS funcao, c.nome_cidade AS cidade
            FROM funcionario a
            INNER JOIN funcao b ON a.id_funcao = b.id_funcao
            INNER JOIN endereco c ON a.id_cidade = c.id_cidade
            WHERE 1=1 $consultar ORDER BY a.nome";

    $result = pg_query(ConnectPG(), $sql);

    $funcionarioLista = '';
    while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {

        $editar = sprintf('<a href="editaFuncionario.php?acao=%s&id_funcionario=%s&id_cidade=%s" title="Editar Funcionário">
                          <img src="img/gear.png"/></a>', ACAO_ALTERAR, $row['id_funcionario'], $row['id_cidade']);

        $apagar = sprintf($apagar = sprintf('<a href="listaFuncionario.php?acao=%s&id_funcionario=%s&id_cidade=%s" 
                title="Excluir Funcionário" data-toggle="modal" data-target="#modalExcluir"><img src="img/x-button.png"/></a>',
            ACAO_APAGAR, $row['id_funcionario'], $row['id_cidade'],$row['nome']));

        //$novo = sprintf('<a href="cadastraFuncionario.php?acao=%s" title="Novo Funcionário"><img src="img/add.png"/></a>', ACAO_INSERT);

        $funcionarioLista .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
            $row['nome'], date('d/m/Y', strtotime($row['data de nascimento'])), $row['email'], date('d/m/Y', strtotime($row['admissao'])), $row['funcao'], $row['cidade'], /*$novo,*/ $apagar, $editar);

    }
} catch (Exception $e) {
    $mensagem = sprintf("<div class='alert alert-danger' role='alert'>
                        <strong>%s</strong></div>", $ex->getMessage());
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <!-- Modal -->
    <div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="modalExcluir" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExcluir"><b>Exclusão de Funcionário</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem Certeza que Deseja Excluir este Funcionário ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <input class="btn btn-outline-success" type="submit" name="btnExcluir" value="Excluir Registro">
                </div>
            </div>
        </div>
    </div>
    <!-- Fim do Modal -->
    <section class="container-fluid">
        <div class="my-5 text-center">
            <span class="h3"><b>Listagem de Funcionários</b></span>
        </div>
        <form action="listaFuncionario.php" method="POST">
            <input type="hidden" name="acao" value="<?php echo $acao; ?>">
            <input type="hidden" name="id_funcionario" value="<?php echo $id_funcionario; ?>">
            <label for="campoConsultar">Pesquisar Funcionário</label>
            <!-- BOTÃO DO INPUT COM IMAGEM -->
            <div class="input-group mb-3">
                <input type="text" name="campoConsultar" class="form-control" aria-label="pesquisaFuncionario" aria-describedby="button-addon2">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2" name="btConsultar">
                        <img src="img/search.svg" alt="Buscar" width="30" height="30" class="mr-1">Pesquisar
                    </button>
                </div>
            </div>
            <?php if ($mensagem != null){echo $mensagem;}?>
            <table class="table table-hover table-responsive-md">
                <caption>Lista de Funcionários</caption>
                <thead>
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Data de Nascimento</th>
                    <th scope="col">email</th>
                    <th scope="col">Data de Admissão</th>
                    <th scope="col">Função</th>
                    <th scope="col">Cidade</th>
                    <th scope="col">Excluir</th>
                    <th scope="col">Editar</th>
                </tr>
                </thead>
                <tbody>
                    <?php echo $funcionarioLista; ?>
                </tbody>
            </table>
        </form>
        <div class="my-5 text-center ml-auto">
            <input type="button" class="btn btn-outline-danger" name="btVoltar" value="Menu Principal"
                   onclick="javascript: location.href='index.php';">
            <input type="button" class="btn btn-outline-success" name="btVoltar" value="Cadastrar Novo Funcionário"
                   onclick="javascript: location.href='cadastraFuncionario.php';">
        </div>
    </section>
    <?php include 'apoio/footer.php'; ?>
</html>
