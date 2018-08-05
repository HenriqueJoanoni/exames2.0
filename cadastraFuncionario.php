<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('nome','data_de_nascimento','telefone','rg','cpf','email','dt_admissao','cidade','bairro','cep','uf','rua');

$funcId = isset($_POST['id_funcao']) ? $_POST['id_funcao'] : '';
$setorId = isset($_POST['id_setor']) ? $_POST['id_setor'] : '';
$cidadeId = isset($_POST['id_cidade']) ? $_POST['id_cidade'] : '';
$ativo = isset($_POST['ativo']) ? $_POST['ativo'] : null;
$mensagem = null;

$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
$paramInsert = ValorInicio($valorInicial);

try {
    if (isset($_POST['btCarregaFuncao'])) {
        $paramInsert = $_POST;
    }

    if (Gravar()) {
        try {

            $funcao = explode("|", $_POST['id_funcao']);
            $cpf = limpaString($_POST['cpf']);
            $tel = limpaString($_POST['telefone']);
            $admissao = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['dt_admissao'])));
            $nascimento = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['data_de_nascimento'])));

            $transac = 0;
            //validaForm();
            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível iniciar a Transação!");}
            $transac = 1;

            $sql = sprintf("INSERT INTO endereco (nome_cidade,logradouro,cep,rua,estado) VALUES (%s,%s,%s,%s,%s)RETURNING id_cidade", 
                            QuotedStr($_POST['cidade']), QuotedStr($_POST['bairro']), QuotedStr($_POST['cep']), 
                            QuotedStr($_POST['rua']), QuotedStr($_POST['uf']));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível cadastrar o endereço");}
            $id_cidade = pg_fetch_array($result,NULL,PGSQL_NUM);
            
            $sql = sprintf("INSERT INTO funcionario (id_funcao, id_cidade, id_setor, nome, dt_nascimento, telefone, rg, cpf,email, dt_admissao,ativo) "
                    . "VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",QuotedStr($funcao[0]), 
                    QuotedStr($id_cidade[0]), QuotedStr($_POST['id_setor']), QuotedStr($_POST['nome']), 
                    QuotedStr($nascimento), QuotedStr($tel), QuotedStr($_POST['rg']), QuotedStr($cpf), QuotedStr($_POST['email']), 
                    QuotedStr($admissao), QuotedStr(@$_POST['ativo']));
            $result = pg_query(ConnectPG(), $sql);
            
            if ($ativo == 's') {
                
                $sql = sprintf("INSERT INTO login (email, senha, ativo) VALUES (%s,%s,%s)", 
                        QuotedStr($_POST['email']), QuotedStr(md5('1234')), QuotedStr($ativo));
                $result = pg_query(ConnectPG(), $sql);

                if (!$result) {
                    foreach ($_POST as $campo => $valor) {
                        $paramInsert[$campo] = $valor;
                    }
                    throw new Exception("Não foi possível Incluir o Funcionário à permissão de login!");
                }
            }

            $result = pg_query(ConnectPG(),'commit');
            if(!$result){throw new Exception("Não foi possível finalizar a transação");}
            
            if (!$result) {
                foreach ($_POST as $campo => $valor) {
                    $paramInsert[$campo] = $valor;
                }

                throw new Exception("Não foi possível cadastrar o usuário!!");
            } else {
//                echo "<SCRIPT type='text/javascript'>
//                        alert('Funcionário cadastrado com Sucesso!');
//                        window.location.replace(\"listaFuncionario.php\");
//                    </SCRIPT>";

                echo "<div class=\"modal fade bd-example-modal-sm\" tabindex=\"-1\" role=\"dialog\" 
                                        aria-labelledby=\"modalSucesso\" aria-hidden=\"true\">
                      <div class=\"modal-dialog modal-sm\">
                        <div class=\"modal-content\">
                          Funcionário cadastrado com Sucesso!
                        </div>
                      </div>
                    </div>";

            }
        } catch (Exception $ex) {
            if ($transac) {
                pg_query(ConnectPG(), 'rollback');
            }
            $mensagem = sprintf("<div class='alert alert-danger' role='alert'>
                        <strong>%s</strong></div>", $ex->getMessage());
        }
    }
} catch (Exception $e) {
    $mensagem = sprintf("<div class='alert alert-danger' role='alert'>
                        <strong>%s</strong></div>", $ex->getMessage());
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <section class="container">
        <div class="my-5 text-center">
            <span class="h3"><b>Cadastro de Funcionários</b></span>
        </div>
        <?php if ($mensagem != null){echo $mensagem;}?>
        <form action="cadastraFuncionario.php" method="POST">
            <input type="hidden" name="acao" value="<?php echo $acao; ?>">
            <input type="hidden" name="id_funcionario" value="<?php echo $funcId; ?>">
            <fieldset id="coluna1">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="nome" class="form-control" id="nameInput" aria-describedby="nameHelp"
                           placeholder="Nome do Funcionário" name="nome" autofocus value="<?php echo $paramInsert['nome']; ?>">
                </div>
                <div class="form-group">
                    <label for="dataNascimento">Data de Nascimento</label>
                    <input type="date" class="form-control" id="data_de_nascimento"  name="data_de_nascimento" placeholder="dd/mm/aaaa" value="<?php echo $paramInsert['data_de_nascimento']; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="example@mail.com" value="<?php echo $paramInsert['email']; ?>">
                </div>
                <div class="form-group">
                    <label for="rg">RG</label>
                    <input type="text" class="form-control" id="rg" name="rg" placeholder="Número de RG" value="<?php echo $paramInsert['rg']; ?>">
                </div>
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" onkeydown="mascara(this,cpfMask);" value="<?php echo $paramInsert['cpf']; ?>">
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" placeholder="(xx)xxxxx-xxxx" onkeydown="telmask(this)" value="<?php echo $paramInsert['telefone']; ?>">
                </div>
                <div class="form-group">
                    <label for="setor">Setor</label>
                    <select class="form-control bg-light" id="inputSetor">
                        <?php echo GetSetor($setorId,null); ?>
                    </select>
                </div>
                <div class="form-group form-check col-6">
                    <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="s">
                    <label class="form-check-label" for="ativo">Permitir Acesso</label>
                    <small class="form-text text-muted">Define se o Funcionário terá permissão de login no sistema.</small>
                </div>
            </fieldset>
            <fieldset id="coluna2">
                <div class="form-group">
                    <label for="dataAdmissao">Data de Admissão</label>
                    <input type="date" class="form-control" id="data_admissao" name="dt_admissao" value="<?php echo $paramInsert['dt_admissao']; ?>">
                </div>
                <div class="form-group">
                    <label for="cep">Cep</label>
                    <input name="cep" type="text" class="form-control" id="cep" value="<?php echo $paramInsert['cep']; ?>" placeholder="00000-000" onblur="pesquisacep(this.value);">
                </div>
                <div class="form-group">
                    <label for="rua">Rua</label>
                    <input name="rua" type="text" class="form-control" id="rua" placeholder="Nome da Rua" value="<?php echo $paramInsert['rua'];?>">
                </div>
                <div class="form-group">
                    <label for="bairro">Bairro</label>
                    <input name="bairro" type="text" class="form-control" id="bairro" placeholder="Nome do Bairro" value="<?php echo $paramInsert['bairro'];?>">
                </div>
                <div class="form-group">
                    <label for="cidade">Cidade</label>
                    <input name="cidade" type="text" class="form-control" id="cidade" placeholder="Nome da Cidade" value="<?php echo $paramInsert['cidade'];?>">
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <input name="uf" type="text" class="form-control" id="uf" placeholder="UF" value="<?php echo $paramInsert['uf'];?>">
                </div>
                <div class="form-group">
                    <label for="funcao">Função</label>
                    <select class="form-control bg-light" id="inputFuncao">
                        <?php echo GetFuncao($setorId,$funcId); ?>
                    </select>
                </div>
            </fieldset>
            <div class="clearfix"></div>
            <button type="button" name="btCancelar" class="btn btn-outline-danger" onclick="javascript: window.history.back();">Cancelar</button>
            <button type="submit" class="btn btn-outline-secondary" name="btEnviar">Cadastrar</button>
        </form>
    </section>
    <?php include 'apoio/footer.php'; ?>
</html>