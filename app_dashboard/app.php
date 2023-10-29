<?php

    Class Dashboard {
        public $data_inicio;
        public $data_fim;
        public $numeroVendas;
        public $totalVendas;
        public $clientesAtivos;
        public $clientesInativos;
        public $TTelogios;
        public $TTreclamacoes;
        public $TTsugestoes;
        public $TTdespesas;
        

        public function __get($attr) {
            return $this->$attr;
        }

        public function __set($attr, $val){
            $this->$attr = $val;
            return $this;
        }
    }

    class Conexao {
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';


        public function conectar() {

            try {
                $conexao = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname",
                    "$this->user",
                    "$this->pass"
                );
                $conexao->exec('set names utf8');

                return $conexao;
            } catch(PDOExeption $e) {
                echo '<p>'.$e->getMessege().'</p>';
            }
        }
    }
     
    class Bd{
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao, Dashboard $dashboard) {
            $this->conexao = $conexao->conectar();
            $this->dashboard =  $dashboard;
        }

        public function getNumeroDeVendas() {
            $query = "
                        select
                            count(*) as numero_vendas
                        from 
                            tb_vendas
                        where 
                                data_venda between :data_inicio and :data_fim ";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;

        }

        public function getTTvendas() {
            $query = "
                        select
                            SUM(total) as total_vendas
                        from 
                            tb_vendas
                        where 
                                data_venda between :data_inicio and :data_fim ";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;

        }

        
        public function getTTclientesA() {
            $query = "
                        select
                            count(*) as total
                        from 
                            tb_clientes
                        where 
                            cliente_ativo = :id";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', 1);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total;

        }

        public function getTTclientesD() {
            $query = "
                        select
                            count(*) as total
                        from 
                            tb_clientes
                        where 
                            cliente_ativo = :id";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', 0);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total;

        }
        public function getTTdespesas() {
            $query = "
                        select
                            SUM(total) as total
                        from 
                            tb_despesas
                        where 
                                data_despesa between :data_inicio and :data_fim ";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total;

        }

        public function contato($id) {
                
            $query = "
                        select
                            count(*) as total
                        from 
                            tb_contatos
                        where 
                            tipo_contato = :id";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total;

        }
        
    }


    $dashboard = new Dashboard();
    $conexao = new Conexao();

    $competencia = explode('-',$_GET['competencia']); 
    $ano = $competencia[0];
    $mes = $competencia[1];

    $dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
    $dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dias);
    $bd = new Bd($conexao, $dashboard);

    $dashboard->__set('numeroVendas', $bd->getNumeroDeVendas());
    $dashboard->__set('totalVendas', $bd->getTTvendas());
    $dashboard->__set('clientesAtivos', $bd->getTTclientesA());
    $dashboard->__set('clientesInativos', $bd->getTTclientesD());
    $dashboard->__set('TTdespesas', $bd->getTTdespesas());
    for($i = 1; $i < 4; $i++) {
        if($i == 1) {
            $dashboard->__set('TTreclamacoes', $bd->contato($id = 1));
        } elseif ($i == 2) {
            $dashboard->__set('TTelogios', $bd->contato($id = 2));
            
        } else {
            $dashboard->__set('TTsugestoes', $bd->contato($id = 3));
        }
    }
    
    echo json_encode($dashboard);
 

    

?>