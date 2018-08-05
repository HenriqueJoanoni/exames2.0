<?php
require_once 'apoio/valida.php';
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <section class="container-fluid">
        <div class="my-5 text-center">
            <span class="h5"><b>Escolha uma Opção</b></span>
        </div>
        <div class="my-5 align-content-center">
            <div class="card-deck">
                <div class="card text-center">
                    <a href="cadastraFuncionario.php"><img class="card-img-top py-2" src="img/add-user.png" alt="Cadastrar Funcionário" width="100" height="280"></a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="cadastraFuncionario.php"><b>Cadastrar Funcionário</b></a></h5>
                    </div>
                </div>
                <div class="card text-center">
                    <a href="listaFuncionario.php"><img class="card-img-top px-2" src="img/employees.png" alt="Listar Funcionário" width="70" height="280"></a>
                    <div class="card-body">
                        <a href="listaFuncionario.php"><h5 class="card-title"><b>Listar Funcionários</b></h5></a>
                    </div>
                </div>
                <div class="card text-center">
                    <a href="cadastraClinica.php"><img class="card-img-top px-5" src="img/hospital-antigo.png" alt="Cadastra Clínica" width="70" height="280"></a>
                    <div class="card-body">
                        <a href="cadastraClinica.php"><h5 class="card-title"><b>Cadastrar Clínica</b></h5></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="container-fluid">
        <div class="my-5 align-content-center">
            <div class="card-deck">
                <div class="card text-center">
                    <a href="geraExame.php"><img class="card-img-top p-4" src="img/examination.png" alt="Gerar Exame" width="100" height="280"></a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="geraExame.php"><b>Gerar Exame</b></a></h5>
                    </div>
                </div>
                <div class="card text-center">
                    <a href="historicoFuncional.php"><img class="card-img-top px-5" src="img/medical-history.png" alt="Histórico Funcional" width="70" height="280"></a>
                    <div class="card-body">
                        <a href="historicoFuncional.php"><h5 class="card-title"><b>Histórico Funcional</b></h5></a>
                    </div>
                </div>
                <div class="card text-center">
                    <a href="listaClinica.php"><img class="card-img-top py-3 px-5" src="img/hospital.png" alt="Lista Clínica" width="70" height="280"></a>
                    <div class="card-body">
                        <a href="listaClinica.php"><h5 class="card-title"><b>Listar Clínica</b></h5></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include 'apoio/footer.php'; ?>
</html>
