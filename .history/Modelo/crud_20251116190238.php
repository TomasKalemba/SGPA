<?php

class crud
{
    private $db;
    private $Servidor;
    private $Usuarios;
    private $Senha_db;
    private $Conexao;

    function __construct()
    {
        $this->Servidor = 'localhost';
        $this->Usuarios = 'root';
        $this->Senha_db = '';
        $this->db = 'sgpa';

        try {
            $this->Conexao = new PDO("mysql:host=" . $this->Servidor . ";port=3306;dbname=" . $this->db, $this->Usuarios, $this->Senha_db);
            $this->Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $Erro) {
            echo "Erro ao conectar com o servidor: " . $Erro->getMessage();
            exit();
        }
    }

    public function getConexao()
    {
        return $this->Conexao;
    }

    public function Inserir($tabela, array $Dados)
    {
        if ($this->getConexao() != null && is_array($Dados)) {
            try {
                $tamanho = count($Dados);
                $Campo = array_keys($Dados);
                $Valores = array_values($Dados);
                for ($i = 0; $i < $tamanho; $i++) {
                    $Vector[$i] = "`" . $Campo[$i] . "`=? ";
                }
                $Vectores = implode(", ", $Vector);
                $sql = "INSERT INTO `" . $tabela . "` SET " . $Vectores . "";
                set_time_limit(0);
                $Exec = $this->getConexao()->prepare($sql);
                $Exec->execute($Valores);
                return $this->getConexao()->lastInsertId();
            } catch (PDOException $Erro) {
                echo "Erro ao Inserir na base de Dados: " . $Erro->getMessage();
                exit();
            }
        } else {
            echo "Não foi possível conectar com o banco de dados";
            exit();
        }
    }

    public function Editar($tabela, array $Dados, $Condicao, $paramsCondicao = [])
    {
        if ($this->getConexao() != null && is_array($Dados)) {
            try {
                $tamanho = count($Dados);
                $Campo = array_keys($Dados);
                $Valores = array_values($Dados);
    
                $Vector = [];
                for ($i = 0; $i < $tamanho; $i++) {
                    $Vector[$i] = "`" . $Campo[$i] . "` = ? ";
                }
                $Vectores = implode(", ", $Vector);
    
                $sql = "UPDATE `" . $tabela . "` SET " . $Vectores . " WHERE " . $Condicao;
    
                set_time_limit(0);
                $Exec = $this->getConexao()->prepare($sql);
    
                // Junta os valores do SET com os parâmetros da condição
                $Exec->execute(array_merge($Valores, $paramsCondicao));
    
                return $Exec->rowCount();
            } catch (PDOException $Erro) {
                echo "Erro ao Editar na base de Dados: " . $Erro->getMessage();
                return false;
            }
        } else {
            echo "Não foi possível conectar com o banco de dados ou dados inválidos";
            return false;
        }
    }
    

    public function Eliminar($tabela, $Condicao)
    {
        if ($this->getConexao() != null) {
            try {
                $sql = "DELETE FROM " . $tabela . " WHERE " . $Condicao;
                set_time_limit(0);
                $Exec = $this->getConexao()->prepare($sql);
                $Exec->execute();
                return $Exec->rowCount();
            } catch (PDOException $Erro) {
                echo "Erro ao Eliminar na base de Dados: " . $Erro->getMessage();
            }
        } else {
            echo "Não foi possível conectar com o banco de dados";
        }
    }

    // Buscar docentes inativos (ativo = 0)
    public function buscarDocentesInativos()
    {
        try {
            $sql = "SELECT id, nome, email FROM usuarios WHERE tipo = 'Docente' AND ativo = 0";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            echo "Erro ao buscar docentes inativos: " . $erro->getMessage();
            return [];
        }
    }

    // Buscar docentes ativos (ativo = 1)
    public function buscarDocentesAtivos() {
        try {
            $sql = "SELECT id, nome, email FROM usuarios WHERE tipo = 'Docente' AND ativo = 1";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            echo "Erro ao buscar docentes ativos: " . $erro->getMessage();
            return [];
        }
    }

    // Ativar conta de docente (ativo = 1)
    public function ativarDocente($id) {
        try {
            $sql = "UPDATE usuarios SET ativo = 1 WHERE id = ? AND tipo = 'Docente'";
            $stmt = $this->getConexao()->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $erro) {
            echo "Erro ao ativar docente: " . $erro->getMessage();
            return false;
        }
    }

    public function buscarPorId($tabela, $id) {
        $stmt = $this->getConexao()->prepare("SELECT * FROM $tabela WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Desativar conta de docente (ativo = 0)
    public function desativarDocente($id)
    {
        try {
            $sql = "UPDATE usuarios SET ativo = 0 WHERE id = ?";
            $stmt = $this->getConexao()->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $erro) {
            echo "Erro ao desativar docente: " . $erro->getMessage();
            return false;
        }
    }

    // Eliminar definitivamente um docente
    public function eliminarDocente($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id = ? AND tipo = 'Docente'";
            $stmt = $this->getConexao()->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $erro) {
            echo "Erro ao eliminar docente: " . $erro->getMessage();
            return false;
        }
    }

    // Buscar estudantes
    public function buscarEstudantes($somenteAtivos = false) {
        try {
            $sql = "SELECT id, nome, email, ativo, ano_curricular 
                    FROM usuarios 
                    WHERE tipo = 'Estudante'";
            if ($somenteAtivos) {
                $sql .= " AND ativo = 1";
            }
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar estudantes: " . $e->getMessage();
            return [];
        }
    }

    public function eliminarEstudante($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id = ? AND tipo = 'Estudante'";
            $stmt = $this->getConexao()->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            echo "Erro ao eliminar estudante: " . $e->getMessage();
            return false;
        }
    }

    // Contar registros
    public function contarRegistros($sql, $params = []) {
        try {
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            echo "Erro ao contar registros: " . $e->getMessage();
            return 0;
        }
    }

    public function buscarEstudantesPorGrupo($grupo_id)
    {
        try {
            $sql = "SELECT u.nome, u.email, u.ano_curricular
                    FROM grupo_estudante ge
                    JOIN usuarios u ON ge.estudante_id = u.id
                    WHERE ge.grupo_id = ?";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$grupo_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar estudantes do grupo: " . $e->getMessage();
            return [];
        }
    }

    // Verificação de login via cookie
    public function verificarLoginPorCookie() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'], $_COOKIE['user_token'])) {
            $userId = $_COOKIE['user_id'];
            $token  = $_COOKIE['user_token'];

            $stmt = $this->getConexao()->prepare("SELECT id, nome, token_login FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && hash_equals($user['token_login'], $token)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nome']    = $user['nome'];
            } else {
                setcookie("user_id", "", time() - 3600, "/");
                setcookie("user_token", "", time() - 3600, "/");
            }
        }
    }

    // 🔹 Métodos específicos para ano curricular
    public function getAnoCurricular($id) {
        try {
            $sql = "SELECT ano_curricular FROM usuarios WHERE id = ?";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            echo "Erro ao buscar ano curricular: " . $e->getMessage();
            return null;
        }
    }
    public function buscarEstudantesComCurso() {
    $sql = "SELECT u.id, u.nome, u.email, u.numero_matricula, u.ano_curricular, 
                   c.nome AS curso_nome
            FROM usuarios u
            LEFT JOIN cursos c ON u.curso_id = c.id
            WHERE u.tipo = 'Estudante'";
    $stmt = $this->getConexao()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function atualizarAnoCurricular($id, $ano) {
        try {
            $sql = "UPDATE usuarios SET ano_curricular = ? WHERE id = ?";
            $stmt = $this->getConexao()->prepare($sql);
            return $stmt->execute([$ano, $id]);
        } catch (PDOException $e) {
            echo "Erro ao atualizar ano curricular: " . $e->getMessage();
            return false;
        }
    }

}
