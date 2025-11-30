<?php
include_once("../Modelo/usuarios.php");
include_once("../Modelo/crud.php");

class login extends usuarios {
    function __construct() {
        ob_start();
        if (session_status() === PHP_SESSION_NONE) session_start();
        parent::__construct();
    }

    // Config hash (Argon2id ou bcrypt)
    private function argonOptions(): array {
        return [
            'memory_cost' => 1 << 17,
            'time_cost'   => 4,
            'threads'     => 2,
        ];
    }

    private function hashPassword(string $plain): string {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($plain, PASSWORD_ARGON2ID, $this->argonOptions());
        }
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    private function needsArgon2idRehash(string $hash): bool {
        if (!defined('PASSWORD_ARGON2ID')) return false;
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, $this->argonOptions());
    }

    // ============================================================
    // LOGIN
    // ============================================================
    public function logar($email, $senha) {
        $email = trim($email);
        $senha = trim($senha);

        $_SESSION['old_email'] = $email;

        if (empty($email) || empty($senha)) {
            $_SESSION['mensagem'] = 'Preencha todos os campos.';
            $_SESSION['tipo_alerta'] = 'warning';  // alerta amarelo
            $this->redirecionarLogin();
        }

        $conn = $this->getConexao();

        $stmt = $conn->prepare("
            SELECT u.*, d.nome AS departamento_nome
            FROM usuarios u
            LEFT JOIN departamento d ON u.departamento_id = d.id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);

        if ($stmt->rowCount() === 0) {
            $_SESSION['mensagem'] = 'Erro! Usuário ou senha incorretos.';
            $_SESSION['tipo_alerta'] = 'danger';  // alerta vermelho
            $this->redirecionarLogin();
        }

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashBD = $dados['senha'];
        $loginOk = false;

        $info = password_get_info($hashBD);
        if ($info['algo'] !== 0 && password_verify($senha, $hashBD)) {
            $loginOk = true;

            if ($this->needsArgon2idRehash($hashBD)) {
                $novo = $this->hashPassword($senha);
                $conn->prepare("UPDATE usuarios SET senha=? WHERE id=?")
                     ->execute([$novo, $dados['id']]);
            }
        }

        // Hash legado MD5
        if (!$loginOk && strlen($hashBD) === 32 && ctype_xdigit($hashBD)) {
            if (md5($senha) === $hashBD) {
                $loginOk = true;

                $novo = $this->hashPassword($senha);
                $conn->prepare("UPDATE usuarios SET senha=? WHERE id=?")
                     ->execute([$novo, $dados['id']]);
            }
        }

        if (!$loginOk) {
            $_SESSION['mensagem'] = 'Erro! Usuário ou senha incorretos.';
            $_SESSION['tipo_alerta'] = 'danger';
            $this->redirecionarLogin();
        }

        if ($dados['tipo'] === 'Docente' && (int)$dados['ativo'] === 0) {
            $_SESSION['mensagem'] = 'Conta de docente ainda não foi ativada.';
            $_SESSION['tipo_alerta'] = 'warning';
            $this->redirecionarLogin();
        }

        // LOGIN OK
        unset($_SESSION['old_email']);

        $_SESSION['id']              = $dados['id'];
        $_SESSION['nome']            = $dados['nome'];
        $_SESSION['tipo']            = $dados['tipo'];
        $_SESSION['email']           = $dados['email'];
        $_SESSION['matricula']       = $dados['numero_matricula'];
        $_SESSION['curso_id']        = $dados['curso_id'] ?? null;
        $_SESSION['departamento_id'] = $dados['departamento_id'] ?? null;
        $_SESSION['departamento']    = $dados['departamento_nome'] ?? null;
        $_SESSION['foto']            = $dados['foto'] ?? '';
        $_SESSION['ano_curricular']  = $dados['ano_curricular'] ?? null;

        // Se quiser mostrar uma mensagem de sucesso ao logar, pode fazer aqui (opcional)
        // $_SESSION['mensagem'] = 'Login realizado com sucesso!';
        // $_SESSION['tipo_alerta'] = 'success';

        // Lembrar-me
        if (isset($_POST['lembrar'])) {
            $token = bin2hex(random_bytes(32));
            $expiracao = date("Y-m-d H:i:s", strtotime("+30 days"));

            $conn->prepare("INSERT INTO user_tokens (user_id, token, expiracao) VALUES (?, ?, ?)")
                 ->execute([$dados['id'], $token, $expiracao]);

            setcookie("lembrar_token", $token, time() + (86400 * 30), "/", "", isset($_SERVER['HTTPS']), true);
        } else {
            setcookie("lembrar_token", "", time() - 3600, "/", "", isset($_SERVER['HTTPS']), true);
        }

        $this->redirecionarPainel($dados['tipo']);
    }

    // ============================================================
    // REGISTRO
    // ============================================================
    public function criarconta(
        $nome, $tipo, $email, $senha,
        $matricula, $curso_id, $departamento_id = null,
        $ano_curricular = null, $foto = null
    ) {
        // Guardar valores antigos para repopular formulário
        $_SESSION['old_nome']         = $nome;
        $_SESSION['old_email']        = $email;
        $_SESSION['old_tipo']         = $tipo;
        $_SESSION['old_matricula']    = $matricula;
        $_SESSION['old_curso_id']     = $curso_id;
        $_SESSION['old_departamento'] = $departamento_id;
        $_SESSION['old_ano']          = $ano_curricular;

        $nome  = trim($nome);
        $email = trim($email);
        $senha = trim($senha);
        $matricula = trim($matricula);

        if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
            $_SESSION['mensagem'] = 'Preencha todos os campos obrigatórios.';
            $_SESSION['tipo_alerta'] = 'warning';
            $this->redirecionarRegistro();
        }

        if ($tipo === "Estudante" && (empty($matricula) || empty($curso_id) || empty($ano_curricular))) {
            $_SESSION['mensagem'] = 'Matrícula, curso e ano curricular são obrigatórios para estudantes.';
            $_SESSION['tipo_alerta'] = 'warning';
            $this->redirecionarRegistro();
        }

        if ($tipo === "Docente" && empty($departamento_id)) {
            $_SESSION['mensagem'] = 'Departamento é obrigatório para docentes.';
            $_SESSION['tipo_alerta'] = 'warning';
            $this->redirecionarRegistro();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['mensagem'] = 'E-mail inválido.';
            $_SESSION['tipo_alerta'] = 'danger';
            $this->redirecionarRegistro();
        }

        if (strlen($senha) < 6) {
            $_SESSION['mensagem'] = 'A senha deve ter pelo menos 6 caracteres.';
            $_SESSION['tipo_alerta'] = 'warning';
            $this->redirecionarRegistro();
        }

        if ($this->existeEmail($email)) {
            $_SESSION['mensagem'] = 'E-mail já está em uso.';
            $_SESSION['tipo_alerta'] = 'danger';
            $this->redirecionarRegistro();
        }

        if ($this->existeNome($nome)) {
            $_SESSION['mensagem'] = 'Nome de usuário já está em uso.';
            $_SESSION['tipo_alerta'] = 'danger';
            $this->redirecionarRegistro();
        }

        // Upload da foto
        $nomeFotoFinal = null;
        if ($foto && $foto['error'] === 0) {
            $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $nomeFotoFinal = "foto_" . uniqid() . "." . $ext;

            $dir = '../visao/fotos/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            move_uploaded_file($foto['tmp_name'], $dir . $nomeFotoFinal);
        }

        // Ajustar departamento para estudantes
        if ($tipo === 'Estudante' && $curso_id) {
            $conn = (new crud())->getConexao();

            $stmt = $conn->prepare("SELECT departamento_id FROM cursos WHERE id = ?");
            $stmt->execute([$curso_id]);

            $departamento_id = $stmt->fetchColumn();
        }

        // Salvar no banco
        $this->setNome($nome);
        $this->setEmail($email);
        $this->setTipo($tipo);
        $this->setSenha($this->hashPassword($senha));
        $this->setNumeroMatricula($matricula);
        $this->setCursoId($curso_id ?: null);
        $this->setDepartamentoId($departamento_id ?: null);
        $this->setAnoCurricular($ano_curricular);
        $this->setAtivo($tipo === 'Estudante' ? 1 : 0);
        $this->setFoto($nomeFotoFinal);

        if ($this->InserirUsuario() > 0) {
            $_SESSION['mensagem'] = 'Conta criada com sucesso.';
            $_SESSION['tipo_alerta'] = 'success'; // mensagem verde
            unset($_SESSION['old_nome'], $_SESSION['old_email'], $_SESSION['old_tipo'], $_SESSION['old_matricula'], $_SESSION['old_curso_id'], $_SESSION['old_departamento'], $_SESSION['old_ano']);
            $this->redirecionarSucesso();  // para sucesso.php
        } else {
            $_SESSION['mensagem'] = 'Erro! Não foi possível criar esta conta.';
            $_SESSION['tipo_alerta'] = 'danger';
            $this->redirecionarRegistro();
        }
    }

    // REDIRECIONAMENTOS
    private function redirecionarPainel($tipo) {
        $url = $this->baseUrl();
        switch ($tipo) {
            case 'Estudante': header("Location: $url/SGPA/Visao/indexEstudante.php"); break;
            case 'Docente'  : header("Location: $url/SGPA/Visao/indexDocente.php");   break;
            case 'Admin'    : header("Location: $url/SGPA/Visao/indexAdmin.php");     break;
        }
        exit();
    }

    private function redirecionarLogin() {
        $url = $this->baseUrl();
        header("Location: $url/SGPA/Visao/login.php");
        exit();
    }

    private function redirecionarRegistro() {
        $url = $this->baseUrl();
        header("Location: $url/SGPA/Visao/register.php");
        exit();
    }

    private function redirecionarSucesso() {
        $url = $this->baseUrl();
        header("Location: $url/SGPA/Visao/sucesso.php");
        exit();
    }

    private function baseUrl() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
            ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    }
}

// --- Processamento ---
$logar = new login();

if (isset($_POST['logar'])) {
    $logar->logar($_POST['email'], $_POST['senha']);
}

if (isset($_POST['criarconta'])) {
    $logar->criarconta(
        $_POST['nomeUsuario'],
        $_POST['tipoUsuario'],
        $_POST['emailUsuario'],
        $_POST['senhaUsuario'],
        $_POST['numeroMatricula'] ?? null,
        $_POST['curso_id'] ?? null,
        $_POST['departamento_id'] ?? null,
        $_POST['ano_curricular'] ?? null,
        $_FILES['fotoUsuario'] ?? null
    );
}

if (isset($_GET['sair'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (isset($_COOKIE['lembrar_token'])) {
        $stmt = (new crud())->getConexao()
            ->prepare("DELETE FROM user_tokens WHERE token = ?");
        $stmt->execute([$_COOKIE['lembrar_token']]);

        setcookie("lembrar_token", "", time() - 3600, "/");
    }

    session_unset();
    session_destroy();

    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
        ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/SGPA/Visao/login.php";

    header('Location: ' . $url);
    exit();
}
?>
