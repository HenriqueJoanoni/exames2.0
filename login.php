<?php
include_once 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('email', 'senha', 'ativo');
$paramInsert = ValorInicio($valorInicial);
$mensagem = null;

// VERIFICA SE FOI ENVIADO AS VARIÁVEIS ATRAVÉS DO POST
if (isset($_POST['paramLogin']) && $_POST['paramLogin'] != "" && $_POST['paramSenha'] != "") {

    // RECEBE AS VARIÁVEIS VIA POST E TRATA O SQL INJECTION FINALIZANDO COM A CODIFICAÇÃO MD5
        $LoginPost = pg_escape_string($_POST['paramLogin']);
        $senhaPost = pg_escape_string($_POST['paramSenha']);

    // VERIFICA SE EXISTE USUÁRIOS CADASTRADOS COM O LOGIN E SENHA INFORMADO
    $sql = sprintf("SELECT id_login,email,senha,ativo FROM login WHERE email = %s AND senha = %s", 
                    QuotedStr($LoginPost), QuotedStr(md5($senhaPost)));
    $result = pg_query(ConnectPG(), $sql);
    $Res = pg_fetch_assoc($result);

    // VERIFICA SE ACHOU ALGUM USUÁRIO CADASTRADO CASO CONTRÁRIO DÁ UM ALERTA PARA O USUÁRIO
    try{
        if (!$Res) {
            foreach($_POST as $campo=>$valor){
                $paramInsert[$campo] = $valor;
            }
            throw new Exception("ALERTA: Login ou senha inválidos!");
        }elseif($Res['ativo'] != 's'){

            foreach($_POST as $campo=>$valor){
                $paramInsert[$campo] = $valor;
            }

            throw new Exception("ALERTA: Este usuário não tem permissão de login!");
        }else {
            // CRIA AS SESSÕES DE VALIDAÇÃO DAS PAGINAS
            session_start();
            $_SESSION['IDlogin'] = $Res['id_login'];
            $_SESSION['paramLogin'] = $Res['email'];
            $_SESSION['paramSenha'] = $Res['senha'];

            header("Location: index.php");
        }
    }catch (Exception $ex){
        $mensagem = sprintf("<div class='alert alert-danger' role='alert'>
                        <strong>%s</strong></div>", $ex->getMessage());
    }
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <section class="container">
        <div class="my-5 text-center">
            <span class="h5 d-block"><b>Fazer Login</b></span>
        </div>
        <div class="row my-5 justify-content-center">
            <div class="card" style="width: 30rem;">
                <div class="card-body">
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="login">Login</label>
                            <input type="email" name="paramLogin" class="form-control" id="loginEmail" aria-describedby="emailHelp"
                                   placeholder="Example@email.com" value="<?php echo @$paramInsert['paramLogin']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="senha">Senha</label>
                            <input type="password" name="paramSenha" class="form-control" id="loginSenha" aria-describedby="passHelp"
                                   placeholder="*****" value="<?php echo @$paramInsert['paramSenha']; ?>">
                        </div>
                        <button type="submit" class="btn btn-outline-secondary" name="btLogin">Fazer Login</button>
                        <small class="form-text text-muted">Esqueceu sua Senha? <a href="redefineSenha.php">Clique Aqui</a></small>
                    </form>
                    <?php if ($mensagem != null){echo $mensagem;}?>
                </div>
            </div>
        </div>
    </section>
    <?php include 'apoio/footer.php'; ?>
</html>
