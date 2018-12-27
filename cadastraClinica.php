<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('nome_lab', 'cnpj', 'telefone', 'cep', 'rua', 'bairro', 'cidade', 'uf');
$obrigatorio = $valorInicial;
$mensagem = null;

$labCod = 0;
$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
$paramInsert = ValorInicio($valorInicial);
try {
    if (Gravar()) {
        $paramInsert = @pg_escape_string($_POST);
        
        $telefone = limpaString($_POST['telefone']);
        $cnpj = limpaString($_POST['cnpj']);

        try {
            $transac = 0;
            //validaForm();
            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível inciar a transação!");}
            $transac = 1;

            $sql = sprintf("INSERT INTO endereco (nome_cidade,logradouro,cep,rua,estado) VALUES (%s,%s,%s,%s,%s)RETURNING id_cidade", 
                            QuotedStr($_POST['cidade']), QuotedStr($_POST['bairro']), QuotedStr($_POST['cep']), 
                            QuotedStr($_POST['rua']), QuotedStr($_POST['uf']));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível cadastrar o endereço");}
            $id_endereco = pg_fetch_array($result,NULL,PGSQL_NUM);

            $sql = sprintf("INSERT INTO laboratorio (nome_lab,telefone,cnpj,id_endereco) VALUES(%s,%s,%s,%s)", 
                            QuotedStr($_POST['nome_lab']), QuotedStr($telefone), QuotedStr($cnpj), QuotedStr($id_endereco[0]));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível cadastrar a clínica!");}

            $result = pg_query(ConnectPG(), 'commit');
            if (!$result) {throw new Exception("Não foi possível finalizar a transação");}

            if (!$result) {

                foreach ($_POST as $campo => $valor) {
                    $paramInsert[$campo] = $valor;
                }

                throw new Exception("Não foi possível cadastrar a clínica!!");
            }

            echo "<SCRIPT type='text/javascript'>
                    alert('Clínica cadastrada com Sucesso!');
                    window.location.replace(\"listaClinica.php\");
                    </SCRIPT>";
        } catch (Exception $ex) {
            if ($transac) {
                pg_query(ConnectPG(), 'rollback');
            }
            $mensagem = $ex->getMessage();
            Alert($mensagem);
        }
    }
} catch (Exception $e) {
    $mensagem = $e->getMessage();
    Alert($mensagem);
}
?>
<html>
<?php include 'apoio/header.php'; ?>
<section class="container">
    <div class="my-5 text-center">
        <span class="h3"><b>Cadastro de Funcionários</b></span>
    </div>
    <?php if ($mensagem != null){echo $mensagem;}?>
    <form action="cadastraClinica.php" method="POST">
        <input type="hidden" name="acao" value="<?php echo $acao; ?>">
        <input type="hidden" name="id_lab" value="<?php echo $labCod; ?>">
        <div class="form-group">
            <label for="nomeClinica">Nome do Laboratório</label>
            <input type="text" class="form-control" name="nome_lab" id="nomeClinica" placeholder="Laboratório" autofocus 
                   aria-describedby="nomeClinica" value="<?php echo $paramInsert['nome_lab']; ?>">
        </div>
        <div class="form-group">
            <label for="cnpj">CNPJ</label>
            <input type="text" class="form-control" name="cnpj" id="cnpjClinica" placeholder="00.000.000/0000-00" aria-describedby="cnpjClinica"
            onkeypress="mascara(this,cnpjMask)" value="<?php echo $paramInsert['cnpj']; ?>">
        </div>
        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="text" class="form-control" name="telefone" id="telefone" placeholder="(00)00000-0000" aria-describedby="telefoneClinica"
            onkeypress="telmask(this)" value="<?php echo $paramInsert['telefone']; ?>">
        </div>
        <div class="form-group">
            <label for="cep">CEP</label>
            <input type="text" class="form-control" name="cep" id="cep" placeholder="00000-000" aria-describedby="cepClinica"
                   value="" onblur="pesquisacep(this.value);">
        </div>
        <div class="form-group">
            <label for="rua">Rua</label>
            <input type="text" class="form-control" name="rua" id="rua" placeholder="Nome da Rua" aria-describedby="ruaClinica">
        </div>
        <div class="form-group">
            <label for="bairro">Bairro</label>
            <input type="text" class="form-control" name="bairro" id="bairro" placeholder="Nome do Bairro" aria-describedby="bairoClininca">
        </div>
        <div class="form-group">
            <label for="cidade">Cidade</label>
            <input type="text" class="form-control" name="cidade" id="cidade" placeholder="Nome da Cidade" aria-describedby="cidadeClinica">
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <input type="text" class="form-control" name="estado" id="uf" placeholder="UF" aria-describedby="estadoClinica">
        </div>
        <div class="row ml-auto">
            <button type="button" name="btCancelar" class="btn btn-outline-danger" onclick="javascript: window.history.back();">Cancelar</button>
            <button type="submit" class="btn btn-outline-secondary" name="btEnviar">Cadastrar</button>
        </div>
    </form>
</section>
<?php include 'apoio/footer.php'; ?>
</html>



