<?php
include './apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('email', 'senha', 'ativo');
$paramEmail = isset($_POST['paramEmail']) ? $_POST['paramEmail'] : '';
$paramSenha = isset($_POST['paramSenha']) ? $_POST['paramSenha'] : '';
$confirmaSenha = isset($_POST['repeteSenha']) ? $_POST['repeteSenha'] : '';
$paramInsert = ValorInicio($valorInicial);
$mensagem = null;

$acao = isset($_POST['btCancelar']) ? ACAO_CANCELAR : isset($_REQUEST['acao']) ? isset($_REQUEST['acao']) : ACAO_ALTERAR;
try {
    if (Gravar()) {
        $sql = sprintf("SELECT email FROM login WHERE email = %s", QuotedStr(pg_escape_string($paramEmail)));
        $result = pg_query(ConnectPG(), $sql);

        if (!pg_num_rows($result)) {

            foreach ($_POST as $campo => $valor) {
                $paramInsert[$campo] = $valor;
            }

            throw new Exception("Email Inválido");
        } elseif (md5 ($_POST['paramSenha']) !== md5 ($_POST['repeteSenha'])) {

            foreach ($_POST as $campo => $valor) {
                $paramInsert[$campo] = $valor;
            }
            throw new Exception("As senhas não conferem!");
        }elseif ($_POST['paramEmail'] && !$_POST['paramSenha'] || !$_POST['repeteSenha']) {
            
            foreach ($_POST as $campo => $valor) {
                $paramInsert[$campo] = $valor;
            }
            throw new Exception("Existem campos vazios !");
            
        }else {
            $sql = sprintf("UPDATE login SET senha = %s WHERE email = %s", QuotedStr(md5($paramSenha)), QuotedStr(pg_escape_string($paramEmail)));
            $result = pg_query(ConnectPG(), $sql);

            //Alert("Senha Alterada com sucesso!");
            $mensagem = '<div class="alert alert-success" role="alert">Senha Alterada com sucesso!</div>';
        }
    }
} catch (Exception $ex) {
    $mensagem = sprintf("<div class='alert alert-danger' role='alert'>
                        <strong>%s</strong></div>", $ex->getMessage());
}
?>
<html>
<?php include 'apoio/header.php'; ?>
<section class="container">
    <div class="my-5 text-center">
        <span class="h5 d-block"><b>Redefinir Senha</b></span>
    </div>
    <div class="row my-5 justify-content-center">
        <div class="card" style="width: 30rem;">
            <div class="card-body">
                <form action="redefineSenha.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="paramEmail" class="form-control" id="redefineLoginEmail" aria-describedby="redefineEmail"
                               placeholder="Example@email.com" value="<?php echo @$paramInsert['paramEmail']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="novasenha">Nova Senha</label>
                        <input type="password" name="paramSenha" class="form-control" id="redefineLoginSenha" aria-describedby="redefineSenha"
                               placeholder="*****" value="<?php echo @$paramInsert['paramSenha']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="repetesenha">Repita a Senha</label>
                        <input type="password" name="repeteSenha" class="form-control" id="repeteSenha" aria-describedby="repetesenha"
                               placeholder="*****" value="<?php echo @$paramInsert['repeteSenha']; ?>">
                    </div>
                    <button class="btn btn-outline-secondary" type="button" name="btCancelar" onclick="javascript: location.href='login.php'">Cancelar</button>
                    <button type="submit" class="btn btn-outline-secondary" name="btEnviar">Redefinir Senha</button>
                </form>
                <?php if ($mensagem != null){echo $mensagem;}?>
            </div>
        </div>
    </div>
</section>
<?php include 'apoio/footer.php'; ?>
</html>

