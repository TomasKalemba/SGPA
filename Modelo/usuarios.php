<?php
require_once 'crud.php';

class usuarios extends crud {
    private $Id;
    private $Nome;
    private $Tipo;
    private $Email;
    private $Senha;
    private $NumeroMatricula;
    private $CursoId;
    private $DepartamentoId; // ✅ corrigido
    private $AnoCurricular; 
    private $Ativo;
    private $Foto;

    function __construct() {
        parent::__construct();
    }

    // ===========================
    // Getters
    // ===========================
    public function getId()              { return $this->Id; }
    public function getNome()            { return $this->Nome; }
    public function getTipo()            { return $this->Tipo; }
    public function getEmail()           { return $this->Email; }
    public function getSenha()           { return $this->Senha; }
    public function getNumeroMatricula() { return $this->NumeroMatricula; }
    public function getCursoId()         { return $this->CursoId; }
    public function getDepartamentoId()  { return $this->DepartamentoId; } // ✅ corrigido
    public function getAnoCurricularUsuario() { return $this->AnoCurricular; }
    public function getAtivo()           { return $this->Ativo; }
    public function getFoto()            { return $this->Foto; }

    // ===========================
    // Setters
    // ===========================
    public function setId($Id)                          { $this->Id = $Id; }
    public function setNome($Nome)                      { $this->Nome = $Nome; }
    public function setTipo($Tipo)                      { $this->Tipo = $Tipo; }
    public function setEmail($Email)                    { $this->Email = $Email; }
    public function setSenha($Senha)                    { $this->Senha = $Senha; }
    public function setNumeroMatricula($NumeroMatricula){ $this->NumeroMatricula = $NumeroMatricula; }
    public function setCursoId($CursoId)                { $this->CursoId = $CursoId; }
    public function setDepartamentoId($DepartamentoId)  { $this->DepartamentoId = $DepartamentoId; } // ✅ corrigido
    public function setAnoCurricular($AnoCurricular)    { $this->AnoCurricular = $AnoCurricular; }
    public function setAtivo($Ativo)                    { $this->Ativo = $Ativo; }
    public function setFoto($Foto)                      { $this->Foto = $Foto; }

    // ===========================
    // Inserir novo usuário
    // ===========================
    public function InserirUsuario() {
        $Dados = [
            'nome'              => $this->getNome(),
            'tipo'              => $this->getTipo(),
            'email'             => $this->getEmail(),
            'senha'             => $this->getSenha(),
            'numero_matricula'  => $this->getNumeroMatricula(),
            'curso_id'          => $this->getCursoId(),
            'departamento_id'   => $this->getDepartamentoId(), // ✅ corrigido
            'ano_curricular'    => $this->getAnoCurricularUsuario(),
            'ativo'             => $this->getAtivo(),
            'foto'              => $this->getFoto(),
        ];
        return $this->Inserir('usuarios', $Dados);
    }

    // ===========================
    // Editar usuário
    // ===========================
    public function EditarUsuario() {
        $Dados = [
            'nome'              => $this->getNome(),
            'tipo'              => $this->getTipo(),
            'email'             => $this->getEmail(),
            'senha'             => $this->getSenha(),
            'numero_matricula'  => $this->getNumeroMatricula(),
            'curso_id'          => $this->getCursoId(),
            'departamento_id'   => $this->getDepartamentoId(), // ✅ corrigido
            'ano_curricular'    => $this->getAnoCurricularUsuario(),
            'ativo'             => $this->getAtivo(),
            'foto'              => $this->getFoto(),
        ];

        if ($this->Editar('usuarios', $Dados, "id='" . $this->getId() . "'") > 0) {
            echo "Usuário editado com sucesso!";
        } else {
            echo "Erro ao editar usuário.";
        }
    }

    // ===========================
    // Eliminar usuário
    // ===========================
    public function EliminarUsuario() {
        if ($this->Eliminar('usuarios', "id='" . $this->getId() . "'") > 0) {
            echo "Usuário eliminado com sucesso!";
        } else {
            echo "Erro ao eliminar usuário.";
        }
    }

    // ===========================
    // Verifica se o email já existe
    // ===========================
    public function existeEmail($email) {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $this->getConexao()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    // ===========================
    // Verifica se o nome já existe
    // ===========================
    public function existeNome($nome) {
        $sql = "SELECT id FROM usuarios WHERE nome = ?";
        $stmt = $this->getConexao()->prepare($sql);
        $stmt->execute([$nome]);
        return $stmt->rowCount() > 0;
    }
}
